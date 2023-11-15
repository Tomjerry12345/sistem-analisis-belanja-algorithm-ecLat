<?php
class Eclat {

    private $transactions = [];
    private $minSupport;
    private $frequentItemSets = [];

    public function __construct(array $transactions, $minSupport = 2) {
        $this->transactions = $transactions;
        $this->minSupport = $minSupport;
    }

    public function run() {
        $items = $this->getUniqueItems();
        $totalTransactions = count($this->transactions);

        foreach ($items as $item) {
            $tidList = $this->getTidListForItem($item);
            $supportCount = count($tidList);

            if ($supportCount >= $this->minSupport) {
                $support = $supportCount / $totalTransactions;
                $this->frequentItemSets[] = [
                    'item' => $item,
                    'tidList' => $tidList,
                    'support' => $support
                ];
            }
        }

        return $this->frequentItemSets;
    }

    private function getUniqueItems() {
        $items = [];
        foreach ($this->transactions as $transaction) {
            foreach ($transaction as $item) {
                if (!in_array($item, $items)) {
                    $items[] = $item;
                }
            }
        }
        sort($items);
        return $items;
    }

    private function getTidListForItem($item) {
        $tidList = [];
        foreach ($this->transactions as $tid => $transaction) {
            if (in_array($item, $transaction)) {
                $tidList[] = $tid;
            }
        }
        return $tidList;
    }
}

$filename = 'uploads/data.csv'; // Replace with your CSV file name
$data = array();

if (($handle = fopen($filename, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}

$transactions = array();
foreach ($data as $row) {
    $transaction = array_slice($row, 2); // Extracting items from the row
    $transactions[] = $transaction;
}

$eclat = new Eclat($transactions, 2);
$frequentSets = $eclat->run();

$frequentSetsData = [];
foreach ($frequentSets as $frequentSet) {
    if (!empty($frequentSet['item'])) {
        $item = $frequentSet['item'];
        $tidList = $frequentSet['tidList'];
        $support = $frequentSet['support'];
        $supportCount = count($tidList);
        $frequentSetsData[] = [
            'item' => $item,
            'supportCount' => $supportCount,
            'support' => $support
        ];
    }
}

?>