<?php declare(strict_types = 1);

namespace Pd\CssDependenciesVersion;

interface IVersion
{

	public function version(string $url): string;

}
