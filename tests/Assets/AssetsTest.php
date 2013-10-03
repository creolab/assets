<?php

use Mockery as m;
use Assets\Assets;
use Illuminate\Foundation\Application;

class AssetsTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		// $this->assets = new Assets(m::mock('Application'));
	}

	public function testCreatingNewCollectionReturnsCollectionInstance()
	{
		// $this->assertInstanceOf('Assets\Collection', $this->assets->addCollection('css', 'test'));
		// $this->assertInstanceOf('Assets\Collection', $this->assets->addCollection('js', 'test'));
		// $this->assertInstanceOf('Assets\Collection', $this->assets->addJsCollection('test'));
		// $this->assertInstanceOf('Assets\Collection', $this->assets->addCssCollection('test'));
	}

}
