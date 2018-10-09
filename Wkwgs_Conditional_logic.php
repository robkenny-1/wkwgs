
/*
    "Conditional Logic" Copyright (C) 2018 

    Conditional Logic is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


class Wkwgs_Evaluator
{
	protected $Data			= null;
	protected $Operators	= null;

	public function __construct( $operators, $data = null )
	{
		$this->Data = $data;
	}

	public function Evaluate($operator, $arg1 = null, $arg2 = null)
	{
		$operator_description = $this->Data[$operator];
		if (!is_null($operator_description))
		{
			int $operands      = $operator_description['Operands'];
			$operator_function = $operator_description['Eval'];

			if ($operands === 0)
			{
				return $operator_function();
			}
			else if ($operands === 1)
			{
				return $operator_function($arg1);
			}
			else if ($operands === 2)
			{
				return $operator_function($arg1, $arg2);
			}
		}
		return false;
	}
}
class Wkwgs_Product_Values extends Wkwgs_Evaluator
{
	public function __construct( $product )
	{
		parent::__construct($Operators, $product)
	}

	private $Operators		= array(
		'Product_Type'		=> array(
			'Operands'		=> 0,
			'Eval'			=> array( $this, 'Product_Type'),
		),
		'SKU'				=> array(
			'Operands'		=> 0,
			'Eval'			=> array( $this, 'sku'),
		),
		'Variable_Type'		=> array(
			'Operands'		=> 0,
			'Eval'			=> array( $this, 'Variable_Type'),
		),
	)
	private function Product_Type()
	{
		return $this->Data->get_type();
	}
	private function sku()
	{
		return $this->Data->get_sku();
	}
	private function Variable_Type()
	{
		return $this->Data->get_type();
	}
}

class Wkwgs_Conditional_Logic extends Wkwgs_Evaluator
{
	public function __construct()
	{
		parent::__construct($Operators)
	}

	private $Operators = array(
		'NOT'					=> array(
			'Operands'		=> 1,
			'Eval'			=> array( $this, 'NOT'),
		),
		'AND'					=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'AND'),
		),
		'OR'					=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'OR'),
		),
		'EQUAL'					=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'EQUAL'),
		),
		'GREATER_THAN'			=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'GREATER_THAN'),
		),
		'LESS_THAN'				=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'LESS_THAN'),
		),
		'GREATER_THAN_EQUAL'	=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'GREATER_THAN_EQUAL'),
		),
		'LESS_THAN_EQUAL'		=> array(
			'Operands'		=> 2,
			'Eval'			=> array( $this, 'LESS_THAN_EQUAL'),
		),
	)

	private function NOT($arg1)
	{
		return ! $arg1;
	}

	private function AND($arg1, $arg2)
	{
		return $arg1 and $arg2;
	}

	private function OR($arg1, $arg2)
	{
		return $arg1 or $arg2;
	}

	private function EQUAL($arg1, $arg2)
	{
		return $arg1 == $arg2;
	}

	private function GREATER_THAN($arg1, $arg2)
	{
		return $arg1 > $arg2;
	}

	private function LESS_THAN($arg1, $arg2)
	{
		return $arg1 < $arg2;
	}

	private function GREATER_THAN_EQUAL($arg1, $arg2)
	{
		return $arg1 >= $arg2;
	}

	private function LESS_THAN_EQUAL($arg1, $arg2)
	{
		return $arg1 <= $arg2;
	}
}
