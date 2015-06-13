<?php

namespace SqlParser\Fragments;

use SqlParser\Fragment;
use SqlParser\Lexer;
use SqlParser\Parser;
use SqlParser\Token;
use SqlParser\TokensList;

/**
 * `WHERE` keyword parser.
 */
class WhereKeyword extends Fragment
{

    /**
     * Logical operators that can be used to chain expressions.
     *
     * @var array
     */
    private static $OPERATORS = array('&&', '(', ')', 'AND', 'OR', 'XOR', '||');

    /**
     * Whether this fragment is an operator.
     *
     * @var bool
     */
    public $isOperator = false;

    /**
     * The condition.
     *
     * @var string
     */
    public $condition;

    /**
     * @param Parser $parser
     * @param TokensList $list
     * @param array $options
     *
     * @return WhereKeyword[]
     */
    public static function parse(Parser $parser, TokensList $list, array $options = array())
    {
        $ret = array();

        $expr = new WhereKeyword();

        for (; $list->idx < $list->count; ++$list->idx) {
            /** @var Token Token parsed at this moment. */
            $token = $list->tokens[$list->idx];

            // End of statement.
            if ($token->type === Token::TYPE_DELIMITER) {
                break;
            }

            // Skipping whitespaces and comments.
            if (($token->type === Token::TYPE_WHITESPACE) || ($token->type === Token::TYPE_COMMENT)) {
                continue;
            }

            // Conditions are delimited by logical operators.
            if (in_array($token->value, static::$OPERATORS, true)) {
                if (!empty($expr->tokens)) {
                    $ret[] = $expr;
                }

                $expr = new WhereKeyword();
                $expr->isOperator = true;
                $expr->condition = $token->value;
                $epxr->tokens[] = $token;
                $ret[] = $expr;

                $expr = new WhereKeyword();

                continue;
            }

            // No keyword is expected.
            if ($token->type === Token::TYPE_KEYWORD) {
                break;
            }

            $expr->tokens[] = $token;
            $expr->condition .= $token->token;

        }

        // Last iteration was not processed.
        if (!empty($expr->tokens)) {
            $ret[] = $expr;
        }

        --$list->idx;
        return $ret;
    }
}
