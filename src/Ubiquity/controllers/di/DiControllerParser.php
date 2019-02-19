<?php

namespace Ubiquity\controllers\di;

use Ubiquity\orm\parser\Reflexion;
use Ubiquity\utils\base\UArray;

/**
 * Parse the controllers for dependancy injections
 *
 * Ubiquity\controllers\di$DiControllerParser
 * This class is part of Ubiquity
 *
 * @author jcheron <myaddressmail@gmail.com>
 * @version 1.0.0
 * @since Ubiquity 2.1
 *
 */
class DiControllerParser {
	protected $injections = [ ];

	public function parse($controllerClass, $config) {
		$instance = new $controllerClass ();
		$properties = Reflexion::getProperties ( $instance );
		foreach ( $properties as $property ) {
			$propName = $property->getName ();
			$annot = Reflexion::getAnnotationMember ( $controllerClass, $propName, "@injected" );
			if ($annot !== false) {
				$name = $annot->name;
				$this->injections [$propName] = $this->getInjection ( $name, $config, @$annot->code );
			} else {
				$annot = Reflexion::getAnnotationMember ( $controllerClass, $propName, "@autowired" );
				if ($annot !== false) {
					$type = Reflexion::getPropertyType ( $controllerClass, $propName );
					if ($type !== false) {
						$this->injections [$propName] = "function(\$controller){return new " . $type . "();}";
					}
				}
			}
		}
	}

	protected function getInjection($name, $config, $code = null) {
		if ($code != null) {
			return "function(\$controller){return " . $code . ";}";
		}
		if (isset ( $config ["di"] )) {
			$di = $config ['di'];
			if (isset ( $di [$name] )) {
				return '$config["di"]["' . $name . '"]';
			} else {
				throw new \Exception ( "key " . $name . " is not present in config di array" );
			}
		} else {
			throw new \Exception ( "key di is not present in config array" );
		}
	}

	public function __toString() {
		return "return " . UArray::asPhpArray ( $this->injections, "array" ) . ";";
	}

	/**
	 *
	 * @return multitype:
	 */
	public function getInjections() {
		return $this->injections;
	}
}

