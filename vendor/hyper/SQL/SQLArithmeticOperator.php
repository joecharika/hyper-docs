<?php


namespace Hyper\SQL;


abstract class SQLArithmeticOperator
{
    /**
     * Addition - Adds values on either side of the operator
     */
    const plus = '+';

    /**
     * Subtraction - Subtracts right hand operand from left hand operand
     */
    const minus = '-';

    /**
     * Multiplication - Multiplies values on either side of the operator
     */
    const multiply = '*';


    /**
     * Division - Divides left hand operand by right hand operand
     */
    const divide = '/';

    /**
     * Modulus - Divides left hand operand by right hand operand and returns remainder
     */
    const modulus = '%';

}