<?php
$xml = simplexml_load_string($response);
$xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
$elm = $xml->xpath('//return');
$this->token = ($elm[0] != NULL) ? $elm[0] : FALSE ;