<?php

/**
 * Provides easy handling with (X)HTML elements
 *
 * Nodes are an easy way to handle HTML elements inspired by
 * jQuery. When you get a tag from the CMS you can
 * easily add and change some attributes or append and remove 
 * child nodes e.g. Thanks to the toString functionality it can
 * also be used as a simple string.
 */
class WNode {

    /**
     * Defines the tag name of the node, e.g. img
     *
     * @var string
     */
    private $tagName;

    /**
     * Containing child nodes
     *
     * @var array (WNode, string)
     */
    private $childNodes;

    /**
     * The attributes of the node
     *
     * @var array
     */
    private $attributes;
    
    /**
     * List of possible HTML attributes
     * 
     * Taken from 
     * http://de.selfhtml.org/html/referenz/attribute.htm#universalattribute
     * 
     * @var array
     */
    private static $possibleAttributes = array(
		'title', 'name', 'class', 'id', 'style', 'value', 'type', 'dir',
		'lang', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup',
		'onmouseover', 'onmousemove', 'onmouseout', 'onkeypress',
		'onkeydown', 'onkeyup', 'accesskey', 'charset', 'coords', 'href',
		'hreflang', 'onblur', 'onfocus', 'rel', 'rev', 'shape', 
		'tabindex', 'target', 'align', 'alt', 'archive', 'code',
		'codebase', 'height', 'hspace', 'object', 'vspace', 'width',
		'nohref', 'color', 'face', 'size', 'dir', 'cite', 'alink',
		'background', 'bgcolor', 'link', 'onload', 'onunload', 'text',
		'vlink', 'clear', 'disabled', 'valign', 'span', 'char', 'charoff',
		'datetime', 'compact', 'onreset', 'onsubmit', 'method', 'accept',
		'enctype', 'noresize', 'scrolling', 'src', 'frameborder',
		'longdesc', 'marginwidth', 'marginheight', 'cols', 'rows', 
		'profile', 'noshade', 'version', 'ismap', 'usemap', 'hspace',
		'checked', 'maxlength', 'readonly', 'prompt', 'for', 'content',
		'http-equiv', 'scheme', 'classid', 'archive', 'standby', 'selected'
    );

	/**
	 *
	 * @param string $tagName Name of the element
	 */
    public function __construct($tagName) {
        $this->tagName = $tagName;
        $this->attributes = array();
        $this->childNodes = array();
        return $this;
    }
    
    /**
	 * Get a node with optional content
	 */
	public static function get($elementName, $content = null, $attributes = null) {
		$n = new WNode($elementName);
		if ($content !== null) {
			$n->append($content);
		}
		if ($attributes !== null) {
			$n->attribute($attributes);
		}
		return $n;
	}

    /**
     * Sets the tag name for the element, e.g. img
     *
     * @param string $name
     */
    public function setTagName($name) {
        $this->tagName = $name;
        return $this;
    }

    /**
     * Insert conent to the end of the element
     *
     * @param mixed $childNode
     */
    public function append($childNode) {
        $this->childNodes[] = $childNode;
        return $this;
    }
    
    /**
     * Insert content to the beginning of the element
     *
     * @param mixed $childNode
     */
    public function prepend($childNode) {
        array_unshift($this->childNodes, $childNode);
        return $this;
    }

    /**
     * Returns an array of all WNode child nodes
     *
     * @return array child nodes
     */
    public function children() {
		$out = array();
		foreach ($this->childNodes as $n) {
			if (is_object($n)) {
				array_push($out, $n);
			}
		}
        return $out;
    }
    
    /**
     * Returns an array of all content elements (text and WNode objects)
     *
     * @return array child nodes
     */
    public function contents() {
        return $this->childNodes;
    }
    
    /**
     * Get/set unescaped content for the element
     * 
     * TODO Would be nice to convert HTML tags into WNode objects here.
     */
    public function html($content = null) {
		if ($content !== null) {
			$this->childNodes = array($content);
			return $this;
		} else {
			return $this->__toString();
		}
		
	}
	
	/**
	 * Get/set escaped text content
	 */
	public function text($content = null) {
		if ($content !== null) {
			$this->childNodes = array(htmlspecialchars($content));
			return $this;
		} else {
			return strip_tags($this->__toString());
		}
	}
    
    /**
     * Get or set one or more attributes
     */
    public function attribute($attribute, $value = null) {
		// Check if first parameter is a key/value pair array
		if (is_array($attribute) && count($attribute) > 0) {
			foreach ($attribute as $key => $val) {
				$this->attribute($key, $val);
			}
			return $this;
		}
		if ($value !== null) {
			$this->attributes[$attribute] = $value;
			return $this;
		} else {
			if (array_key_exists($attribute, $this->attributes)) {
				return $this->attributes[$attribute];
			}
			return null;
		}
	}

	/**
	 * Convert object to string
	 */
    public function __toString() {
		$o = "<".$this->tagName;
		foreach ($this->attributes as $att => $val) {
			if ($val === true) $val = $att;
			elseif ($val === false) continue;
			$o .= " ".$att."=\"".$val."\"";
		}
        if (count($this->childNodes) === 0) {          
			$o .= "/>";
        } else {
			$o .= ">";
			if (isset($this->childNodes) && is_array($this->childNodes)) {
				foreach ($this->childNodes as $child) {
					$o .= $child;
				}
			}
			$o .= "</".$this->tagName.">";
        }
		return $o;
    }

	/*
	 * == Handy Attributes =================================================
	 */
	 
	/**
	 * Returns an array of possible HTML attributes
	 * 
	 * @return array
	 */
	public static function getPossibleAttributes() {
		return self::$possibleAttributes;
	}
	
	/**
	 * Gets and sets HTML attributes
	 * 
	 */ 
	public function __call($name, $arguments) {
		if (count($arguments) === 1
			&& in_array($name, self::$possibleAttributes)) {
			return $this->attribute($name, $arguments[0]);
		} elseif (count($arguments) === 0
			&& in_array($name, self::$possibleAttributes)) {
			return $this->attribute($name);
		}
	}
	
	public function addClass($className) {
		$cls = explode(' ', trim($this->attribute('class')));
		$className = trim($className);
		if (!in_array($className, $cls)) $cls[] = $className;
		return $this->attribute('class', implode(' ', $cls));
	}
	
	public function removeClass($className) {
		$cls = explode(' ', trim($this->attribute('class')));
		$className = trim($className);
		$key = array_search($className, $cls);
		if ($key === false) return $this;
		unset($cls[$key]);
		return $this->attribute('class', implode(' ', $cls));
	}
	
	public function hasClass($className) {
		$cls = explode(' ', trim($this->attribute('class')));
		if (in_array($className, $cls)) return true;
		else return false;
	}
	
	public function addStyle($style) {
		$styles = trim($this->style());
		if (substr($styles, -1) === ';') {
			$styles = substr($styles, 0, strlen($styles) - 1);
		}
		$styles = explode(';', $styles);
		$style = trim($style);
		if (substr($style, -1) === ';') {
			$style = substr($style, 0, strlen($style) - 1);
		}
		$styles[] = $style;
		return $this->style(implode(';', $styles) . ';');
	}
}

if (defined('WNODE_SHORT_FUNCTION_ACCESS') && WNODE_SHORT_FUNCTION_ACCESS) {
	function wn($elementName, $content = null) {
		return WNode::get($elementName, $content);
	}
}
