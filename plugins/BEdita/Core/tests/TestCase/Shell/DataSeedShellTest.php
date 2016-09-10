<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\TestSuite\ShellTestCase;
use Cake\ORM\TableRegistry;

/**
 * @covers \BEdita\Core\Shell\DataSeedShell
 */
class DataSeedShellTest extends ShellTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public $autoFixtures = false;

    /**
     * Test validation with invalid table name.
     *
     * @return void
     */
    public function testUnsupportedTable()
    {
        $this->invoke(['data_seed', 'insert', '-t', 'thisTableDoesNotExist']);

        $this->assertAborted();
        $this->assertErrorContains('Table "ThisTableDoesNotExist" is not yet supported');
    }

    /**
     * Test validation with invalid fields.
     *
     * @return void
     */
    public function testInvalidFields()
    {
        $this->invoke(['data_seed', 'insert', '-f', 'someField=someValue,someOtherInvalidField']);

        $this->assertAborted();
        $this->assertErrorContains('Could not parse field "someOtherInvalidField"');
    }

    /**
     * Test entity validation.
     *
     * @return void
     */
    public function testValidationErrors()
    {
        $this->loadFixtures('Users');
        $this->invoke(['data_seed', 'insert', '-t', 'users', '-n', '1', '-f', 'blocked=no']);

         $this->assertAborted();
         $this->assertErrorContains('Entity validation failed');
    }

    /**
     * Test application rules validation.
     *
     * @return void
     */
    public function testBuildRulesErrors()
    {
        $this->loadFixtures('Users');
        $this->invoke(['data_seed', 'insert', '-t', 'users', '-n', '2', '-f', 'username=double']);

         $this->assertAborted();
         $this->assertErrorContains('Application rules failed');
    }

    /**
     * Test seeding of users.
     *
     * @return void
     */
    public function testUserSeeding()
    {
        $this->loadFixtures('Users');

        $Users = TableRegistry::get('Users');
        $before = $Users->find('all')->count();

        $this->invoke(['data_seed', 'insert', '-t', 'users', '-n', '10']);
        $this->assertNotAborted($this->getError());

        $after = $Users->find('all')->count();

        $this->assertEquals($before + 10, $after);
    }
}