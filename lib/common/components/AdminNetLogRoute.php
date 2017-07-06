<?php
/**
 * @author: jiwangli
 * @since: 6/30/16 16:13
 */

class AdminNetLogRoute extends LNetLogRoute
{
	protected function processLogs($logs)
	{
		foreach ($logs as $k => $log)
		{
			$logs[$k][0] = sprintf("name[%s] roleId[%s] %s",
                LAManagerService::getUser(),
                LAManagerService::getRoleId(),
				$log[0]
			);
		}

		parent::processLogs($logs);
	}
}