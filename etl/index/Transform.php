<?php

class Transform
{
    private $filePath;

    public function __construct(string $transformFilePath)
    {
        $this->filePath = $transformFilePath;
    }

    public function transform()
    {
        $content = file_get_contents($this->filePath);

        $clean = trim(preg_replace('/\s\s+/', ' ', $content));
        $clean = str_replace('\n', '', $clean);

        $data = json_decode($clean, true);

        $parsedData = [];
        foreach ($data as $key => $item) {
            $parsedData[$key]['location'] = str_replace([' ', ','], '', $item['location'][0]);
            $parsedData[$key]['title'] = $item['title'][0];
            if (isset($item['price'][0])) {
                $parsedData[$key]['price'] = $item['price'][0];
            } else {
                $parsedData[$key]['price'] = 0;
            }
            $parsedData[$key]['company_name'] = '';
            $parsedData[$key]['kind'] = '';
            $parsedData[$key]['position_level'] = '';
            $parsedData[$key]['number'] = '';
            $parsedData[$key]['position'] = '';

            foreach ($item['params'] as $param) {
                if (strpos($param, 'Nazwa firmy')) {
                    $parsedData[$key]['company_name'] = $this->getParamStr($param);
                }
                if (strpos($param, 'Rodzaj pracy') !== false) {
                    $parsedData[$key]['kind'] = $this->getParamStr($param);
                }
                if (strpos($param, 'Poziom stanowiska') !== false) {
                    $parsedData[$key]['position_level'] = $this->getParamStr($param);
                }
                if (strpos($param, 'Numer referencyjny') !== false) {
                    $parsedData[$key]['number'] = $this->getParamStr($param);
                }
            }
        }

        $parsedData = json_encode($parsedData);
        file_put_contents('transform.json', $parsedData);
    }

    public function getParamStr(string $string)
    {
        $stringAfter = substr($string, strpos($string, ":") + 2);
        $stringBefore = substr($stringAfter, strpos($stringAfter, ',') - 1);
        return $stringAfter;
    }
}