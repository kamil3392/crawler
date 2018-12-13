<?php
class Extract {
	public function __construct(){}
	
	public function extract() {
      $pyscript = 'C:\Users\jakub.koziera\Documents\Projects\Scrapy\crawler\etl\spiders\works.py';
      $python = 'C:\Users\jakub.koziera\AppData\Local\Continuum\anaconda3\python.exe';
      $cmd = "$python $pyscript";
      exec($cmd, $output);
	}
}


