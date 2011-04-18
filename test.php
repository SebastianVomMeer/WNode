<?php
define('WNODE_SHORT_FUNCTION_ACCESS', true);
require_once 'WNode.php';

function expect($name, $value, $reference) {
	$name = $name . '<br/><span style="margin-left:1em;color:#000;">' . htmlspecialchars($value) . '</span>';
	$value = '$value = ' . $value;
	eval($value);
	if ($value == $reference) {
		$out = htmlspecialchars($value);
		if ($reference === true) $out = '<em>true</em>';
		if ($reference === false) $out = '<em>false</em>';
		printf('<p><pre style="margin:0;color:#999;">%s</pre><pre style="color:green;margin:0;">Match     : %s</pre></p>', $name, $out);
	} else {
		printf('<p style="color:red;"><pre style="margin:0;color:#999;">%s</pre>%s</p>',
			$name,
			sprintf('<pre style="color:red;margin:0;">Failed    : %s</pre>', htmlspecialchars($value)).
			sprintf('<pre style="margin:0;">Expected  : %s</pre>', htmlspecialchars($reference))
		);
	}
}

echo '<h1>WNode Testing Page</h1>';

echo '<h2>Tests</h2>';

echo '<h3>Elements</h3>';

expect(
	'Default creation',
	"WNode::get('p', 'Hallo');",
	'<p>Hallo</p>'
);

expect(
	'Standalone creation',
	"WNode::get('input');",
	'<input/>'
);

expect(
	'Short function access',
	"wn('p', 'Hallo');",
	'<p>Hallo</p>'
);

expect(
	'Short function chaining',
	"wn('p')->append('Hallo');",
	'<p>Hallo</p>'
);

echo '<h4>Contents</h4>';

expect(
	'Set unescaped text',
	"WNode::get('p', 'This word is <em>highlighted</em>!');",
	'<p>This word is <em>highlighted</em>!</p>'
);

expect(
	'Set unescaped text #2',
	"WNode::get('p')->html('This word is <em>highlighted</em>!');",
	'<p>This word is <em>highlighted</em>!</p>'
);

expect(
	'Set unescaped text #3',
	"WNode::get('p')->append('This word is ')
		->append(WNode::get('em', 'highlighted'))->append('!');",
	'<p>This word is <em>highlighted</em>!</p>'
);

expect(
	'Set escaped text',
	"WNode::get('p')->text('This word is <em>highlighted</em>!');",
	'<p>This word is &lt;em&gt;highlighted&lt;/em&gt;!</p>'
);

expect(
	'Get unescaped text',
	"WNode::get('p')->append('This word is ')
		->append(WNode::get('em', 'highlighted'))->append('!')
		->html();",
	'<p>This word is <em>highlighted</em>!</p>'
);

expect(
	'Get text only',
	"WNode::get('p')->append('This word is ')
		->append(WNode::get('em', 'highlighted'))->append('!')
		->text();",
	'This word is highlighted!'
);

expect(
	'Get text only #2',
	"WNode::get('p', 'This word is <em>highlighted</em>!')->text();",
	'This word is highlighted!'
);

expect(
	'Get children only',
	"count(WNode::get('p')->append('This word is ')
		->append(WNode::get('em', 'highlighted'))->append('!')
		->children());",
	1
);

expect(
	'Get all contents (including text)',
	"count(WNode::get('p')->append('This word is ')
		->append(WNode::get('em', 'highlighted'))->append('!')
		->contents());",
	3
);

echo '<h3>Attributes</h3>';

echo '<h4>Getter and Setter</h4>';

expect(
	'Magic setters #1',
	"WNode::get('p', 'Hallo')->style('color:red;');",
	'<p style="color:red;">Hallo</p>'
);

expect(
	'Magic setters #2',
	"WNode::get('a', 'Top')->href('#top');",
	'<a href="#top">Top</a>'
);

expect(
	'Magic getters #1',
	"WNode::get('p', 'Hallo')->style('color:blue;')->style();",
	'color:blue;'
);

expect(
	'Magic getters #2:',
	"WNode::get('a', 'Top')->href('#top')->href();",
	'#top'
);

echo '<h4>Set Multiple Attributes as Array</h4>';

expect(
	'Use attribute array',
	"WNode::get('a', 'Top')->attribute(array('href'=>'#top', 'class'=>'internal'));",
	'<a href="#top" class="internal">Top</a>'
);

expect(
	'Use attribute array within constructor',
	"WNode::get('a', 'Top', array('href'=>'#top', 'class'=>'internal'));",
	'<a href="#top" class="internal">Top</a>'
);

echo '<h4>Style and Class: Add, Remove, and Has</h4>';

expect(
	'Add class',
	"WNode::get('p', 'Hallo')->attribute('class', 'a')->attribute('class', 'z')->addClass('b')->attribute('class');",
	'z b'
);

expect(
	'Remove class #1',
	"WNode::get('p', 'Hallo')->attribute('class', 'a')->addClass('z')->addClass('b')
		->removeClass('z')->attribute('class');",
	'a b'
);

expect(
	'Remove class #2',
	"WNode::get('p', 'Hallo')->addClass('a')->addClass('z')->addClass('b')
		->removeClass('a')->attribute('class');",
	'z b'
);

expect(
	'Remove class #3',
	"WNode::get('p', 'Hallo')->addClass('a')->addClass('z')->addClass('b')
		->removeClass('x')->attribute('class');",
	'a z b'
);

expect(
	'Has class #1',
	"WNode::get('p', 'Hallo')->addClass('a')->addClass('b')->hasClass('a');",
	true
);

expect(
	'Has class #2',
	"WNode::get('p', 'Hallo')->addClass('a')->addClass('b')->hasClass('b');",
	true
);

expect(
	'Has class #3',
	"WNode::get('p', 'Hallo')->addClass('a')->addClass('b')->hasClass('z');",
	false
);

expect(
	'Add style',
	"WNode::get('p', 'Hallo')->style('color:red;')->addStyle('padding:0;');",
	'<p style="color:red;padding:0;">Hallo</p>'
);

echo '<h4>Boolean Attributes</h4>';

expect(
	'False attributes',
	"WNode::get('option', 'Dog')->value('dog')->selected(false);",
	'<option value="dog">Dog</option>'
);

expect(
	'Get false attributes',
	"WNode::get('option', 'Dog')->value('dog')->selected(false)->selected();",
	false
);

expect(
	'True attributes',
	"WNode::get('option', 'Cat')->value('cat')->selected(true);",
	'<option value="cat" selected="selected">Cat</option>'
);

expect(
	'Get true attributes',
	"WNode::get('option', 'Cat')->value('cat')->selected(true)->selected();",
	true
);


echo '<h3>Possible Attributes</h3><pre>';
$atts = WNode::getPossibleAttributes();
sort($atts, SORT_STRING);
print_r($atts);
