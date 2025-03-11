<?php

namespace Src\Company\Document\Domain\Repositories;

interface MeasurementRepositoryInterface
{
	public function getAll();

	public function store(array $measurements);

}