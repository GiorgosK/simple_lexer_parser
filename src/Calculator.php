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
   */
  function lexer($expression) {

    if (!$expression) {
      $error = "ERROR: empty expression";
      return $error;
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
        $error = "ERROR: Invalid character (" . $expression[$i] . ") at position" . $i;
        return $error;
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
            $error = "ERROR: Mismatched parentheses!";
            return $error;
          }
        }
        array_pop($op_stack);
      } else {
        $error = "ERROR: Unexpected token $token";
        return $error;
      }
    }

    while ($op_stack) {
      $token = array_pop($op_stack);
      if ($token === '(') {
        $error = "ERROR: Mismatched parentheses!";
        return $error;
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
          $error = "ERROR: Unknown operator " . $token[$i];
          return $error;
        }
      }
    }
    return end($stack);
  }
}
