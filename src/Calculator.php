<?php

namespace Drupal\simple_lexer_parser;

/**
 * Class Calculator.
 *
 * @package Drupal\simple_lexer_parser
 */
class Calculator {

  /**
   * Lexical analysis on a given arithmetic expression
   *
   * @param string $expression
   *   an arithmetic expression in infix notation
   *
   * @return string
   *   The result of the expression in postfix notation
   *
   * @throws \Exception
   *   on following occasions
   *   Empty expression
   *   Invalid character
   *   Mismatched parentheses
   *   Unexpected token
   *
   */
  function lexer($expression) {

    if (!$expression) {
      $error = "Empty expression";
      throw new \Exception($error);
    }

    $precedence = array(
      '+' => 2,
      '-' => 2,
      '/' => 3,
      '*' => 3,
    );
    $whitespace = " \t\n";
    $operators = implode('', array_keys($precedence));
    $simpletokens = $operators . '()';
    $numbers = "0123456789.";
    // for the purpose of comparing only; it's forced to top priority explicitly
    $precedence['('] = 0;
    $precedence[')'] = 0;

    // tokenizer
    $tokens = array();
    for ($i = 0;isset($expression[$i]); $i++) {
      $chr = $expression[$i];
      if (strstr($whitespace, $chr)) {
        // noop, whitespace
      } elseif (strstr($simpletokens, $chr)) {
        $tokens[] = $chr;
      } elseif (strstr($numbers, $chr)) {
        $number = $chr;
        while (isset($expression[$i + 1]) && strstr($numbers, $expression[$i + 1])) {
          $number .= $expression[++$i];
        }
        $tokens[] = floatval($number);
      } else {
        $error = "Invalid character (" . $expression[$i] . ") at position " . $i;
        throw new \Exception($error);
      }
    }

    //simple shunting yard algorithm
    $output_queue = array();
    $op_stack = array();
    while ($tokens) {
      $token = array_shift($tokens);
      if (is_float($token)) {
        $output_queue[] = $token;
      } elseif (strstr($operators, $token)) {
        while ($op_stack && $precedence[end($op_stack)] >= $precedence[$token]) {
          $output_queue[] = array_pop($op_stack);
        }
        $op_stack[] = $token;
      } elseif ($token === '(') {
        $op_stack[] = $token;
      } elseif ($token === ')') {
        while (end($op_stack) !== '(') {
          $output_queue[] = array_pop($op_stack);
          if (!$op_stack) {
            $error = "Mismatched parentheses!";
            throw new \Exception($error);
          }
        }
        array_pop($op_stack);
      } else {
        $error = "Unexpected token $token";
        throw new \Exception($error);
      }
    }

    while ($op_stack) {
      $token = array_pop($op_stack);
      if ($token === '(') {
        $error = "Mismatched parentheses!";
        throw new \Exception($error);
      }
      $output_queue[] = $token;
    }
    if (isset($output_queue)) {
      return implode(' ', $output_queue);
    }
  }

  /**
   * Evaluate an expression given in Postfix (Reverse Polish Notation)
   *
   * @param string $postfix
   *   the expression in postifx notation
   *
   * @return string
   *   The result of the expression
   * @throws \Exception
   *   When the unknown operator is found
   */
  public static function evaluate($postfix) {
    $stack = array();
    $token = explode(" ", trim($postfix));
    $count = count($token);

    for ($i = 0; $i < $count; $i++) {
      $tokenNum = "";

      if (is_numeric($token[$i])) {
        array_push($stack, $token[$i]);
      } else {
        $secondOperand = array_pop($stack);
        $firstOperand = array_pop($stack);

        if ($token[$i] == "*") {
          array_push($stack, $firstOperand * $secondOperand);
        } else if ($token[$i] == "/") {
          array_push($stack, $firstOperand / $secondOperand);
        } else if ($token[$i] == "-") {
          array_push($stack, $firstOperand - $secondOperand);
        } else if ($token[$i] == "+") {
          array_push($stack, $firstOperand + $secondOperand);
        } else {
          $error = "Unknown operator " . $token[$i];
          throw new \Exception($error);
        }
      }
    }
    return end($stack);
  }
}
