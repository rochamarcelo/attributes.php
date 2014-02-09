<?php

namespace Test;

use PHPUnit_Framework_TestCase as TestCase;
use Test\SimpleEntity;

class ChangedTest extends TestCase {

  /**
   * Attribute Configuration - data provider
   *
   * fields:
   *  - [boolean] expected result
   *  - [string]  attribute name
   *  - [hash]    object instance
   *
   * @return  array
   */
  function provider_attributes_configuration() {
    $data[] = [ true,   'employed', ['employed' => []] ];
    $data[] = [ false,  'married',  ['married'  => []] ];
    $data[] = [ 35,     'age',      ['age'    => []] ];
    $data[] = [ 'test', 'name',     ['name'   => []] ];

    return $this->instance_wrapper($data);
  }

  /**
   * Object Instance Wrapper
   *
   * adds an object instance to each incoming hash
   *
   * @param   array   attribute data provider configuration
   *
   * @return  array
   */
  function instance_wrapper($data) {
    return array_map(function($parameters){
      $attributes   = $parameters[count($parameters)-1];
      $instance     = new SimpleEntity();
      $reflection   = new \ReflectionObject($instance);
      $__attributes = $reflection->getProperty('__attributes');
      $__attributes->setAccessible(true);
      $__attributes->setValue($instance, $attributes);
      $parameters[] = $instance;

      return $parameters;
    }, $data);
  }

  /**
   * @test
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_Is_Initially_An_Array($expected, $attribute, $config, $instance) {
    $this->assertInternalType('array', $instance->changed($attribute));
  }

  /**
   * @test
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_Is_Initially_Empty($expected, $attribute, $config, $instance) {
    $this->assertEmpty($instance->changed($attribute));
  }

  /**
   * @test
   * @depends Change_History_Is_Initially_An_Array
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_Is_Initially_False_In_Boolean_Context($expected, $attribute, $config, $instance) {
    $this->assertFalse((boolean) $instance->changed($attribute));
  }

  /**
   * @test
   * @depends Change_History_Is_Initially_An_Array
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_Is_Not_Empty_When_Attribute_Is_Set($expected, $attribute, $config, $instance) {
    $instance->set($attribute, $expected);
    $this->assertNotEmpty($instance->changed($attribute));
  }

  /**
   * @test
   * @depends Change_History_Is_Not_Empty_When_Attribute_Is_Set
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_Count_Is_One_When_Attribute_Is_Set($expected, $attribute, $config, $instance) {
    $instance->set($attribute, $expected);
    $this->assertCount(1, $instance->changed($attribute));
  }

  /**
   * @test
   * @depends Change_History_Is_Initially_An_Array
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_As_Boolean_True_When_Attribute_Is_Set($expected, $attribute, $config, $instance) {
    $instance->set($attribute, $expected);
    $this->assertTrue((boolean) $instance->changed($attribute));
  }

  /**
   * @test
   * @depends Change_History_Is_Initially_An_Array
   * @dataProvider provider_attributes_configuration
   */
  function Change_History_Is_Cleared_When_Cloned($expected, $attribute, $config, $instance) {
    $instance->set($attribute, $expected);
    $cloned = clone $instance;

    $this->assertFalse((boolean) $cloned->changed($attribute));
  }

}
