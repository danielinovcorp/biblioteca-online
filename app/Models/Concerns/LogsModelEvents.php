<?php

namespace App\Models\Concerns;

use App\Services\LoggerApp;

trait LogsModelEvents
{
	public static function bootLogsModelEvents()
	{
		static::created(function ($model) {
			LoggerApp::add(self::moduleName(), 'created', $model);
		});

		static::updated(function ($model) {
			$changes = [
				'old' => $model->getOriginal(),
				'new' => $model->getChanges(),
			];
			LoggerApp::add(self::moduleName(), 'updated', $model, $changes);
		});

		static::deleted(function ($model) {
			LoggerApp::add(self::moduleName(), 'deleted', $model);
		});
	}

	protected static function moduleName(): string
	{
		return property_exists(static::class, 'MODULE_NAME') ? static::$MODULE_NAME : class_basename(static::class);
	}
}
