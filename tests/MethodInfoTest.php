<?php
namespace Koda;

class MethodInfoTest extends \PHPUnit_Framework_TestCase
{


	public function testParse()
	{
		$method = MethodInfo::scan('Koda\Math', 'hypotenuse');
		$this->assertSame('Koda\Math', $method->class);
		$this->assertSame('hypotenuse', $method->method);
		$this->assertSame('Koda\Math::hypotenuse', $method->name);
		$this->assertSame('Calculate hypotenuse', $method->desc);
		$this->assertTrue($method->hasOption('link'));
		$this->assertSame('https://en.wikipedia.org/wiki/Hypotenuse', $method->getOption('link'));
		$this->assertSame(['https://en.wikipedia.org/wiki/Hypotenuse'], $method->getOptions('link'));

		$this->assertCount(3, $method->args);
		$this->assertSame(['leg1', 'leg2', 'round'], array_keys($method->args));
		$this->assertInstanceOf('Koda\ArgumentInfo', $method->args['leg1']);

		// leg1
		$leg1 = $method->args['leg1'];
		$this->assertEquals($method->args['leg1'], $method->getArgument('leg1'));
		$this->assertFalse($leg1->optional);
		$this->assertFalse($leg1->multiple);
		$this->assertSame('float', $leg1->type);
		$this->assertNull($leg1->default);
		$this->assertSame('first cathetus of triangle', $leg1->desc);
		$this->assertSame(0, $leg1->position);
		$this->assertEquals(['unsigned' => ['original' => 'unsigned', 'args' => true]], $leg1->filters);

		// round
		$round = $method->args['round'];
		$this->assertEquals($method->args['round'], $method->getArgument('round'));
		$this->assertTrue($round->optional);
		$this->assertFalse($round->multiple);
		$this->assertSame('int', $round->type);
		$this->assertSame(2, $round->default);
		$this->assertSame('returns the rounded value of hypotenuse to specified precision', $round->desc);
		$this->assertSame(2, $round->position);
		$this->assertEquals(['value' => ['original' => 'value 0..6', 'args' => [0, 6]]], $round->filters);
	}


	public function providerInvokeSimple()
	{
		return [
			[3, 4, 2, 5],
			[3.1, 4.1, 2, 5.14],
			[3.1, 4.1, 0, 5],
		];
	}

	/**
	 * @dataProvider providerInvokeSimple
	 *
	 * @param float $leg1
	 * @param float $leg2
	 * @param int $round
	 * @param $result
	 *
	 * @throws \Koda\Error\InvalidArgumentException
	 */
	public function testInvokeSimple($leg1, $leg2, $round, $result)
	{
		$method = MethodInfo::scan('Koda\Math', 'hypotenuse');
		$this->assertEquals($result, $method->invoke([$leg1, $leg2, $round]));
		$this->assertEquals($result, $method->invoke(["leg2" => $leg2, "leg1" => $leg1, "round" => $round]));
	}

	public function providerInvokeMulti()
	{
		return [
			[[2, 4, 6], 4],
		];
	}

	/**
	 * @dataProvider providerInvokeMulti
	 *
	 * @param $nums
	 * @param $result
	 *
	 * @throws \Koda\Error\InvalidArgumentException
	 */
	public function testInvokeMulti($nums, $result)
	{
		$method = MethodInfo::scan('Koda\Math', 'avg');
		$this->assertEquals($result, $method->invoke(["nums" => $nums]));
	}

}