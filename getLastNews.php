<?
$rss_url="https://lenta.ru/rss";
$count_element=5;

$items = simplexml_load_file($rss_url, "SimpleXMLElement", LIBXML_NOCDATA);
$n=0;

foreach($items->channel->item as $item) {
	print "Название: ".(string)$item->title." \n";
	
	print "Анонс: \n".(string)$item->description."\n";
	print "Ссылка на новость: ".(string)$item->link." \n";
	$n++;
	if($n==$count_element){
		break;
	}
}
?>