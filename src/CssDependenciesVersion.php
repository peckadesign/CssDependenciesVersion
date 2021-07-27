<?php declare(strict_types = 1);

namespace Pd\CssDependenciesVersion;

final class CssDependenciesVersion
{

	private IVersion $version;

	private \Psr\Log\LoggerInterface $logger;

	private string $versionParameter;


	public function __construct(IVersion $version, \Psr\Log\LoggerInterface $logger, string $versionParameter)
	{
		$this->version = $version;
		$this->logger = $logger;
		$this->versionParameter = $versionParameter;
	}


	/**
	 * Nalezne url(URL) v obsahu parametru $input a pokusí se overzovat URL
	 */
	public function process(string $input): string
	{
		$callback = function ($matches): string {
			$match = \trim($matches[1], '"\'');

			if (\strpos($match, 'data:') === 0) {
				return $matches[0];
			}

			if (\strpos($match, '/') !== 0) {
				return $matches[0];
			}

			$url = new \Nette\Http\Url($match);
			$url->setQueryParameter($this->versionParameter, $this->version->version($url->getPath()));

			return \str_replace($match, (string) $url, $matches[0]);
		};

		try {
			$return = \Nette\Utils\Strings::replace($input, '~url\(([^)]+)\)~', $callback);
		} catch (\Nette\Utils\RegexpException $e) {
			$this->logger->error($e->getMessage());
			$return = $input;
		}

		return $return;
	}

}
