<?php declare(strict_types = 1);

namespace Pd\CssDependenciesVersion\Commands;

final class VersionCommand extends \Symfony\Component\Console\Command\Command
{

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $versionParameter;

	/**
	 * @var \Pd\Version\Resolvers\AbsoluteUrlResolver
	 */
	private $absoluteUrlResolver;

	/**
	 * @var \Pd\Version\Resolvers\PathResolver
	 */
	private $pathResolver;


	public function __construct(
		\Psr\Log\LoggerInterface $logger,
		string $versionParameter,
		\Pd\Version\Resolvers\AbsoluteUrlResolver $absoluteUrlResolver,
		\Pd\Version\Resolvers\PathResolver $pathResolver
	)
	{
		parent::__construct();

		$this->logger = $logger;
		$this->versionParameter = $versionParameter;
		$this->absoluteUrlResolver = $absoluteUrlResolver;
		$this->pathResolver = $pathResolver;
	}


	protected function configure(): void
	{
		parent::configure();

		$this->setName('pd:css-dependencies-version:version');
		$this->addOption('baseDir', 'b', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED);
		$this->addArgument('file', \Symfony\Component\Console\Input\InputArgument::REQUIRED);
	}


	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
	{
		$baseDir = $input->getOption('baseDir');
		$file = $input->getArgument('file');

		if (\strpos($file, '/') !== 0) {
			$file = \getcwd() . '/' . $file;
		}

		$version = new class($baseDir, $this->versionParameter, $this->absoluteUrlResolver, $this->pathResolver) implements \Pd\CssDependenciesVersion\IVersion
		{

			/**
			 * @var \Pd\Version\Filter
			 */
			private $version;

			/**
			 * @var string
			 */
			private $versionParameter;


			public function __construct(
				string $directory,
				string $versionParameter,
				\Pd\Version\Resolvers\AbsoluteUrlResolver $absoluteUrlResolver,
				\Pd\Version\Resolvers\PathResolver $pathResolver
			)
			{
				$this->version = new \Pd\Version\Filter($directory, $versionParameter, $absoluteUrlResolver, $pathResolver);
				$this->versionParameter = $versionParameter;
			}


			public function version(string $url): string
			{
				$versionedPath = $this->version->__invoke($url);

				if ($versionedPath === NULL) {
					return \md5($url);
				}

				$versionedUrl = new \Nette\Http\Url($versionedPath);

				return $versionedUrl->getQueryParameter($this->versionParameter);
			}

		};

		$cssDependencyVersion = new \Pd\CssDependenciesVersion\CssDependenciesVersion($version, $this->logger, $this->versionParameter);

		\file_put_contents($file, $cssDependencyVersion->process(\file_get_contents($file)));

		return 0;
	}

}
