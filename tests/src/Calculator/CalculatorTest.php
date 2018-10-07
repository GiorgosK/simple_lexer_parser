<?php
namespace Drupal\Tests\simple_lexer_parser\Calculator;

use Drupal\simple_lexer_parser\Calculator;
use Drupal\Tests\UnitTestCase;

/**
 * Simple test to ensure that asserts pass.
 *
 * @group simple_lexer_parser
 */
class CalculatorTest extends UnitTestCase {

  /**
   * Before a test method is run, setUp() is invoked.
   * Create the required objects.
   */
  public function setUp() {
    $this->Calculator = new Calculator();
  }

  /**
   * @covers Drupal\simple_lexer_parser\Calculator::lexer
   * @dataProvider lexerDataProvider
   */
  public function testLexer($expression, $expectedPostfix) {
    $result = $this->Calculator->lexer($expression);
    $this->assertEquals($result, $expectedPostfix);
  }

  public function lexerDataProvider() {
    return [
      ['1 + 2','1 2 +'],
      ['(2 + 3) * 5','2 3 + 5 *'],
      ['((15 / (7 - (1 + 1))) * 3) - (2 + (1 + 1))','15 7 1 1 + - / 3 * 2 1 1 + + -'],
      ['1.1 + 2','1.1 2 +'],
      ['(1+2.1) * 33 + 200 / 10 +  100','1 2.1 + 33 * 200 10 / + 100 +'],
    ];
  }

  /**
   * @covers Drupal\simple_lexer_parser\Calculator::evaluate
   * @dataProvider evaluateDataProvider
   */
  public function testEvaluate($postfix, $expectedResult) {
    $result = $this->Calculator->evaluate($postfix);
    $this->assertEquals($result, $expectedResult);
  }

  public function evaluateDataProvider() {
    return [
      ['1 2 +','3'],
      ['2 3 + 5 *','25'],
      ['15 7 1 1 + - / 3 * 2 1 1 + + -','5'],
      ['1.1 2 +','3.1'],
      ['1 2.1 + 33 * 200 10 / + 100 +','222.3'],
    ];
  }

  /**
   * Once test method has finished running, whether it succeeded or failed, tearDown() will be invoked.
   * Unset the objects created on setUp.
   */
  public function tearDown() {
    unset($this->Calculator);
  }
}
