--TEST--
MongoDB\Driver\Manager::executeBulkWrite
--FILE--
<?php
include 'tests/utils.inc';

$m = new MongoDB\Driver\Manager("mongodb://localhost:27017");

$c = new MongoDB\Driver\Command( [ 'drop' => 'test'] );
$m->executeCommand( 'demo', $c );

$bw = new MongoDB\Driver\BulkWrite( false );
$_id = $bw->insert( [ 'test' => 'foo', 'i' => 1 ] );
$_id = $bw->insert( [ 'test' => 'foo', 'i' => 2 ] );
$_id = $bw->insert( [ 'test' => 'foo', 'i' => 3 ] );
$_id = $bw->insert( [ 'test' => 'foo', 'i' => 4 ] );
$_id = $bw->insert( [ 'test' => 'foo', 'i' => 5 ] );
$_id = $bw->insert( [ 'test' => 'foo', 'i' => 6 ] );

$bw->update( [ 'i' => [ '$gte' => 4 ] ], [ 'j' => 3 ] );
$bw->update( [ 'i' => [ '$gte' => 4 ] ], [ '$set' => [ 'j' => 5 ] ] );
$bw->update( [ 'i' => [ '$gte' => 4 ] ], [ '$set' => [ 'k' => 5 ] ], [ 'multi' => true ] );

$bw->update( [ 'i' => 7 ], [ '$set' => [ 'new' => true ] ] );
$bw->update( [ 'i' => 8 ], [ '$set' => [ 'new' => true ] ], [ 'upsert' => true ] );
$bw->update( [ 'i' => 9 ], [ '$set' => [ 'new' => true ] ], [ 'upsert' => true ] );
$bw->update( [ 'i' => 10 ], [ '$set' => [ 'new' => true ] ], [ 'upsert' => true ] );
$bw->update( [ 'i' => 11 ], [ '$set' => [ 'new' => true ] ], [ 'upsert' => true ] );

$bw->delete( [ 'i' => [ '$gte' => 10 ] ] );
$bw->delete( [ 'i' => [ '$gte' => 8 ] ], [ 'limit' => 1 ] );

$result = $m->executeBulkWrite( 'demo.test', $bw );
show_obj_properties( $result, [ 'inserted', 'matched', 'modified', 'deleted', 'upserted' ] );
echo 'n_commands: ', $result->n_commands, '; offset: ', $result->offset, "\n";
foreach ( $result->getUpsertedIds() as $upsertedId )
{
	echo $upsertedId->index, ': ', $upsertedId->_id, "\n";
}

$q = new MongoDB\Driver\Query( [] );
var_dump( $m->executeQuery( 'demo.test', $q )->toArray() );
?>
--EXPECTF--
inserted: 6; matched: 4; modified: 4; deleted: 3; upserted: 4; 
n_commands: 16; offset: 11
10: %s
11: %s
12: %s
13: %s
array(7) {
  [0]=>
  object(stdClass)#%d (3) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["test"]=>
    string(3) "foo"
    ["i"]=>
    int(1)
  }
  [1]=>
  object(stdClass)#%d (3) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["test"]=>
    string(3) "foo"
    ["i"]=>
    int(2)
  }
  [2]=>
  object(stdClass)#%d (3) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["test"]=>
    string(3) "foo"
    ["i"]=>
    int(3)
  }
  [3]=>
  object(stdClass)#%d (2) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["j"]=>
    int(3)
  }
  [4]=>
  object(stdClass)#%d (5) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["test"]=>
    string(3) "foo"
    ["i"]=>
    int(5)
    ["j"]=>
    int(5)
    ["k"]=>
    int(5)
  }
  [5]=>
  object(stdClass)#%d (4) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["test"]=>
    string(3) "foo"
    ["i"]=>
    int(6)
    ["k"]=>
    int(5)
  }
  [6]=>
  object(stdClass)#%d (3) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectId)#%d (0) {
    }
    ["i"]=>
    int(9)
    ["new"]=>
    bool(true)
  }
}
