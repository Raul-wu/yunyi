<?php
class LMongoDB extends EMongoDB
{
	private $_mongoConnection;

	public function disConnection()
	{
		$this->_mongoConnection = parent::getConnection();
		parent::close();
	}
}