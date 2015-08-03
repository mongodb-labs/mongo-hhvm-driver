--TEST--
BSON deserialization: unserialize all documents as named class (pclass fallbacks)
--FILE--
<?php
class FallbackClass implements MongoDB\BSON\Unserializable
{
	function bsonUnserialize( array $map )
	{
		$this->fallback = $map;
	}
}

class UnserializableClass implements MongoDB\BSON\Unserializable
{
	function bsonUnserialize( array $map )
	{
		$this->unserialized = $map;
	}
}

class LiquidClass 
{
}

$tests = [
	'{ "foo" : 42 }',
	'{ "foo" : "yes", "__pclass": "RigidClass" }',
	'{ "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "RG9lc05vdEV4aXN0" } }', // DoesNotExist
	'{ "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "VW5zZXJpYWxpemFibGVDbGFzcw==" } }', // UnserializableClass
	'{ "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "TGlxdWlkQ2xhc3M=" } }', // LiquidClass

	'{ "nested" : { "foo" : 42 } }',
	'{ "nested" : { "foo" : "yes", "__pclass": "RigidClass" } }',
	'{ "nested" : { "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "RG9lc05vdEV4aXN0" } } }', // DoesNotExist
	'{ "nested" : { "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "VW5zZXJpYWxpemFibGVDbGFzcw==" } } }', // UnserializableClass
	'{ "nested" : { "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "TGlxdWlkQ2xhc3M=" } } }', // LiquidClass
];

$typemaps = [
	[ 'root' => 'FallbackClass', 'document' => 'FallbackClass' ],
];

foreach ( $typemaps as $typemap )
{
	foreach ( $tests as $test )
	{
		echo $test, "\n";
		$bson = MongoDB\BSON\fromJSON( $test );
		try
		{
			var_dump( MongoDB\BSON\toPHP( $bson, $typemap ) );
		}
		catch ( Exception $e )
		{
			echo $e->getMessage(), "\n";
		}
		echo "\n";
	}
}
?>
--EXPECT--
{ "foo" : 42 }
object(FallbackClass)#1 (1) {
  ["fallback"]=>
  array(1) {
    ["foo"]=>
    int(42)
  }
}

{ "foo" : "yes", "__pclass": "RigidClass" }
object(FallbackClass)#1 (1) {
  ["fallback"]=>
  array(2) {
    ["foo"]=>
    string(3) "yes"
    ["__pclass"]=>
    string(10) "RigidClass"
  }
}

{ "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "RG9lc05vdEV4aXN0" } }
object(FallbackClass)#2 (1) {
  ["fallback"]=>
  array(2) {
    ["foo"]=>
    string(3) "yes"
    ["__pclass"]=>
    object(MongoDB\BSON\Binary)#1 (2) {
      ["data"]=>
      string(12) "DoesNotExist"
      ["subType"]=>
      int(128)
    }
  }
}

{ "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "VW5zZXJpYWxpemFibGVDbGFzcw==" } }
object(FallbackClass)#3 (1) {
  ["fallback"]=>
  array(2) {
    ["foo"]=>
    string(3) "yes"
    ["__pclass"]=>
    object(MongoDB\BSON\Binary)#2 (2) {
      ["data"]=>
      string(19) "UnserializableClass"
      ["subType"]=>
      int(128)
    }
  }
}

{ "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "TGlxdWlkQ2xhc3M=" } }
object(FallbackClass)#4 (1) {
  ["fallback"]=>
  array(2) {
    ["foo"]=>
    string(3) "yes"
    ["__pclass"]=>
    object(MongoDB\BSON\Binary)#3 (2) {
      ["data"]=>
      string(11) "LiquidClass"
      ["subType"]=>
      int(128)
    }
  }
}

{ "nested" : { "foo" : 42 } }
object(FallbackClass)#5 (1) {
  ["fallback"]=>
  array(1) {
    ["nested"]=>
    object(FallbackClass)#4 (1) {
      ["fallback"]=>
      array(1) {
        ["foo"]=>
        int(42)
      }
    }
  }
}

{ "nested" : { "foo" : "yes", "__pclass": "RigidClass" } }
object(FallbackClass)#6 (1) {
  ["fallback"]=>
  array(1) {
    ["nested"]=>
    object(FallbackClass)#5 (1) {
      ["fallback"]=>
      array(2) {
        ["foo"]=>
        string(3) "yes"
        ["__pclass"]=>
        string(10) "RigidClass"
      }
    }
  }
}

{ "nested" : { "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "RG9lc05vdEV4aXN0" } } }
object(FallbackClass)#8 (1) {
  ["fallback"]=>
  array(1) {
    ["nested"]=>
    object(FallbackClass)#7 (1) {
      ["fallback"]=>
      array(2) {
        ["foo"]=>
        string(3) "yes"
        ["__pclass"]=>
        object(MongoDB\BSON\Binary)#6 (2) {
          ["data"]=>
          string(12) "DoesNotExist"
          ["subType"]=>
          int(128)
        }
      }
    }
  }
}

{ "nested" : { "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "VW5zZXJpYWxpemFibGVDbGFzcw==" } } }
object(FallbackClass)#10 (1) {
  ["fallback"]=>
  array(1) {
    ["nested"]=>
    object(FallbackClass)#9 (1) {
      ["fallback"]=>
      array(2) {
        ["foo"]=>
        string(3) "yes"
        ["__pclass"]=>
        object(MongoDB\BSON\Binary)#8 (2) {
          ["data"]=>
          string(19) "UnserializableClass"
          ["subType"]=>
          int(128)
        }
      }
    }
  }
}

{ "nested" : { "foo" : "yes", "__pclass": { "$type": "80", "$binary" : "TGlxdWlkQ2xhc3M=" } } }
object(FallbackClass)#12 (1) {
  ["fallback"]=>
  array(1) {
    ["nested"]=>
    object(FallbackClass)#11 (1) {
      ["fallback"]=>
      array(2) {
        ["foo"]=>
        string(3) "yes"
        ["__pclass"]=>
        object(MongoDB\BSON\Binary)#10 (2) {
          ["data"]=>
          string(11) "LiquidClass"
          ["subType"]=>
          int(128)
        }
      }
    }
  }
}
