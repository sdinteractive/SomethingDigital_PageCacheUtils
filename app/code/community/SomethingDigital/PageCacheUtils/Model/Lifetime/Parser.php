<?php

use SomethingDigital_PageCacheUtils_Exception_ParseException as ParseException;

class SomethingDigital_PageCacheUtils_Model_Lifetime_Parser
{
    protected $string = '';
    protected $pos = 0;
    protected $len = 0;
    protected $value = null;

    public function __construct($args)
    {
        $this->setString($args['string']);
        $this->parse();
    }

    public function toValue()
    {
        return $this->value;
    }

    protected function setString($string)
    {
        $this->string = $string;
        $this->len = strlen($string);
    }

    protected function parse()
    {
        if ($this->string === null || $this->string === 'null') {
            $this->value = null;
        } elseif ($this->string === false || $this->string === 'false') {
            $this->value = false;
        } elseif ($this->string === 'none') {
            // This special value means no caching.
            $this->value = 'none';
        } elseif (trim($this->string, '0..9') === '') {
            // Just seconds already.  KISS.
            $this->value = $this->string;
        } else {
            $this->parseExpression();
        }
    }

    protected function parseExpression()
    {
        // Start at 0.  We'll add for each phrase.
        $this->value = 0;

        // Expressions look like this: "1h 2m10s"
        while ($this->pos < $this->len) {
            $this->parsePhrase();
        }
    }

    protected function parsePhrase()
    {
        $this->eatWhite();
        $number = $this->parseNumber();
        $this->eatWhite();
        $typeFactor = $this->parseType();

        $this->value += $typeFactor * $number;
    }

    protected function eatWhite()
    {
        // Get the length of whitespace starting at pos.  And skip it.
        $whiteLength = strspn($this->string, " \t\r\n", $this->pos);
        $this->pos += $whiteLength;
    }

    protected function parseNumber()
    {
        // Get the length of whitespace starting at pos.  And skip it.
        $numberLength = strspn($this->string, '0123456789', $this->pos);
        if ($numberLength === 0) {
            $this->toss('No number found for phrase', ParseException::NUMBER_MISSING);
        }

        $number = substr($this->string, $this->pos, $numberLength);
        $this->pos += $numberLength;

        return $number;
    }

    protected function parseType()
    {
        if ($this->pos >= $this->len) {
            $this->toss('Unexpected end of phrase', ParseException::UNEXPECTED_EOF);
        }

        // This is the type code, the next character.
        $c = strtolower(substr($this->string, $this->pos, 1));
        $factor = $this->typeCharToFactor($c);
        $this->pos++;

        return $factor;
    }

    protected function typeCharToFactor($c)
    {
        switch ($c) {
        case 'w':
            return 86400 * 7;
        case 'd':
            return 86400;
        case 'h':
            return 3600;
        case 'm':
            return 60;
        case 's':
            return 1;

        default:
            $this->toss('Unexpected character \'' . $c . '\', expecting one of wdhms', ParseException::UNEXPECTED_TYPE);
            // To silence parser warnings.
            return 0;
        }
    }

    protected function toss($message, $code)
    {
        throw new ParseException('Failed parsing ' . $this->string . ' at ' . $this->pos . ': ' . $message, $code);
    }
}
