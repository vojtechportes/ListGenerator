<?php

namespace ListGenerator;
use DomDocument;
use DOMNode;
use DomXpath;
use ReflectionClass;
use ListGenerator\Exception\MissingArguments;
use ListGenerator\Exception\MissingHeadings;

class ListGenerator {
	public $output;
	public $nodeList;
	private $document;
	private $nodeArray;
	private $list;
	private $level;
	private $parent;
	private $initialLevel;

	/**
	* Assigns DomDocument to class property
	* @param object $document Empty DomDocument
	* @param string $html element containing headings
	* @param string $query valid xpath query
	*
	* @return void
	*/
	public function __construct (DOMDocument $document, $html = false, $query = false) {
		if (is_string($html) && is_string($query)) {
			$this->document = $document;		
			$document = new DomDocument();
			$document->loadHTML($html);

			try {
				$finder = new DomXpath($document);
				$this->nodeList = $finder->query($query);
			} catch (Exception $e) {
				$e->getMessage();
			}

			$this->document->loadHTML('<ul></ul>');
			$this->parent = $this->document->getElementsByTagName('ul')->item(0);
		} else {
			$className = get_class($this);
			$classReflection = new ReflectionClass($className);
		    $constructor = $classReflection->getConstructor();
		    $parameters = array();

		    $parameters['required']['number'] = (int) $constructor->getNumberOfParameters();
		    $parameters['provided']['number'] = (int) $constructor->getNumberOfRequiredParameters();

		    if ($parameters['required']['number'] <= 1) {
		    	$parameters['required']['string'] = 'argument';
		    } else {
		    	$parameters['required']['string'] = 'arguments';
		    }

		    if ($parameters['provided']['number'] <= 1) {
		    	$parameters['provided']['string'] = 'argument';
		    } else {
		    	$parameters['provided']['string'] = 'arguments';
		    }

			throw new MissingArguments($className.' requires '.$parameters['required']['number'].' '.$parameters['required']['string'].'. Only '.$parameters['provided']['number'].' valid '.$parameters['provided']['string'].' provided.');
		}
	}

	/**
	* Collects nodes to array and gets list tree DOM
	*
	* @return void
	*/
	public function initialize () {
		$this->getNodeArray();
		$this->getTree(false);		
	}

	/**
	* Gets inner html of element parent
	* @param DOMNode $element
	*
	* @return string
	*/
	private function getInnerHtml (DOMNode $element) { 
	    $innerHTML = ""; 
	    $children  = $element->childNodes;

	    foreach ($children as $child) 
	    { 
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
	    }

	    return $innerHTML; 
	} 	

	/**
	* Gets parent 'ul' element in list, if heading order is incorrect, lsat valid parent 'ul' element is returned
	* @param int $level
	*
	* @return void
	*/
	private function getParent ($level) {
		if ((int) $this->level != (int) $level) {
			if ($this->parent->parentNode && $this->parent->parentNode->nodeType !== 13) {
				$this->parent = $this->parent->parentNode;

				if ($this->parent->tagName == 'ul') {
					$level = $level + 1;
				}

				$this->getParent($level);
			}
		}
	}

	/**
	* Gets node array from nodeList
	*
	* @return void
	*/
	private function getNodeArray () {
		foreach ($this->nodeList as $key => $item) {
			if ($item->tagName{0} === 'h') {
				if ($key === 0) {
					$this->level = $this->initialLevel = (int) $item->tagName{1};
				}

				$this->nodeArray[] = $item;
			}
		}

		if (count($this->nodeArray) === 0) {
			throw new MissingHeadings('Hedings are required to generate list.');
		} 
	}

	/**
	* Gets list tree DOM
	*
	* @return void
	*/
	private function getTree () {
		if ($this->document && $this->nodeArray) {
			foreach ($this->nodeArray as $key => $node) {
				$level = (int) $node->tagName{1};

				if ($level > $this->level) {
					$ul = $this->document->createElement('ul');
					$parent = $this->parent->getElementsByTagName('li');
					$this->parent = $parent->item($parent->length - 1);
					$this->parent = $this->parent->appendChild($ul);

					$this->level = $level;
					$this->getTree();
				} else if ($level < $this->level) {
					$this->getParent($level);

					$this->level = $level;
					$this->getTree();
				} else {
					$target = '#';
					$li = $this->document->createElement('li');
					$li->setAttribute('class', 'sg-level-'.$level);

					if ($id = $node->getAttribute('id')) {
						$target = '#'.$id;
					}

					$a = $this->document->createElement('a');
					$a->setAttribute('href', $target);
					$a->textContent = $node->textContent;
					$li->appendChild($a);
					$this->parent->appendChild($li);

					$this->level = $level;
					unset($this->nodeArray[$key]);
				}

				if (count($this->nodeArray) === 0) {
					break;
				}
			}
		}
	}

	/**
	* Saves DOM html and outputs string
	*
	* @return string
	*/
	public function output () {
		$this->document->saveHTML();
		return $this->getInnerHtml($this->document->getElementsByTagName('body')->item(0));
	}
}