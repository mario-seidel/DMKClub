<?php
namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use DMKClub\Bundle\BasicsBundle\Job\JobExecutor;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;

class ExportPdfHandler implements MassActionHandlerInterface {
	const FLUSH_BATCH_SIZE = 100;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/** @var MemberFeeManager */
	protected $feeManager;

	/** @var \Psr\Log\LoggerInterface */
	private $logger;
	/** @var \DMKClub\Bundle\BasicsBundle\Job\JobExecutor */
	private $jobExecutor;

	/**
	 * @param EntityManager $entityManager
	 * @param TranslatorInterface $translator
	 * @param ServiceLink $securityFacadeLink
	 */
	public function __construct(
			EntityManager $entityManager,
			TranslatorInterface $translator,
			LoggerInterface $logger,
			JobExecutor $jobExecutor,
			MemberFeeManager $feeManager
	) {
		$this->entityManager = $entityManager;
		$this->translator = $translator;
		$this->logger = $logger;
		$this->jobExecutor = $jobExecutor;
		$this->feeManager = $feeManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(MassActionHandlerArgs $args) {
		$data = $args->getData();
		$massAction = $args->getMassAction();
		$options = $massAction->getOptions()->toArray();

		$this->entityManager->beginTransaction();
		try {
			set_time_limit(0);
			$iteration = $this->handleExport($options, $data, $args->getResults());
			$this->entityManager->commit();
		} catch (\Exception $e) {
			$this->entityManager->rollback();
			throw $e;
		}

		return $this->getResponse($args, $iteration);
	}

	/**
	 * @param array $options
	 * @param array $data
	 * @param IterableResultInterface $results
	 * @return int
	 */
	protected function handleExport($options, $data, IterableResultInterface $results) {
		$isAllSelected = $this->isAllSelected($data);
		$iteration = 0;

		$jobData = [
				'data_identifier' => $options['data_identifier'],
				'entity_name' => $options['entity_name'],
		];

		if (array_key_exists('values', $data) && !empty($data['values'])) {
			$jobData['entity_ids'] = $data['values'];
			$iteration = count(explode(',', $data['values']));
		}
		elseif($isAllSelected) {
			$entityIds = [];
			foreach ($results as $result) {
				$entityIds[] = $result->getValue('id');
			}
			$jobData['entity_ids'] = implode(',',$entityIds);

			$iteration++;
		}
		if(array_key_exists('entity_ids', $jobData)) {
			$jobType = 'export';
			$jobName = 'dmkexportpdf';

			$this->jobExecutor->createJob($jobType, $jobName, $jobData, true);
		}

		return $iteration;
	}



	/**
	 * @param array $data
	 * @return bool
	 */
	protected function isAllSelected($data)
	{
		return array_key_exists('inset', $data) && $data['inset'] === '0';
	}

	/**
	 * @param MassActionHandlerArgs $args
	 * @param int $entitiesCount
	 *
	 * @return MassActionResponse
	 */
	protected function getResponse(MassActionHandlerArgs $args, $entitiesCount = 0)
	{
		$massAction      = $args->getMassAction();
		$responseMessage = 'dmkclub.basics.datagrid.action.success_message';
		$responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

		$successful = $entitiesCount > 0;
		$options    = ['count' => $entitiesCount];

		return new MassActionResponse(
				$successful,
				$this->translator->transChoice(
						$responseMessage,
						$entitiesCount,
						['%count%' => $entitiesCount]
				),
				$options
		);
	}

}