
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', null,'big_news');
$userquery= '"fontenay sous bois"';

$str ='https://www.bing.com/news/search?q='.str_replace('"','%22',str_replace('  ','+',$userquery)).'+loc%3afr&qft=interval%3d%227%22&form=PTFTNR';
#$str='https://www.bing.com/news/search?q=fontenay+sous+bois&go=Rechercher&qs=ds&form=QBNT';
$curl = curl_init($str);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept-Language: fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5']);
#curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept-Language: en-US,en;q=0.9']);
curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36');

$page = curl_exec($curl);


if(!empty($curl)) { //if any html is actually returned
    $data=[];
    $data['b_query'][]=$str;
    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    $DOM->loadHTML($page);
    libxml_clear_errors();

    $DOM = new DOMXPath($DOM);
    #snippet
    $titre = $DOM->query('//a[@class="title"]');
    foreach($titre as $t){
    $data['b_title'][]=($t->textContent);
    $data['b_support'][]=($t->getAttribute('data-author'));
    $data['b_link'][]=($t->getAttribute('href'));
    }
    $titre = $DOM->query('//div[@class="snippet"]');
    foreach($titre as $t){
    #print_r($t->getAttribute('data-author'));
    $data['b_description'][]=($t->textContent);
    }


    

    if (!file_exists(getcwd().'/photos')) {
        mkdir(getcwd().'/photos', 0777, true);
    }
        $div = $DOM->query('//div[@class="news-card-body card-with-cluster"]');
        #define('DIRECTORY', 'C:/xampp/htdocs/big_news/photos');
        foreach($div as $d){
        $image = $d->getElementsByTagName('img')[0];
        $time = hrtime(true);
        $data['b_timestamp'][]=$time;
        if (!empty($image)){
        $id=($image->getAttribute('id'));
        #x=_ge('emb1283D03AC');if(x){x.src='
            print($id);
            print('<br>');
        
        $pages_nbr= explode("x=_ge('".$id."');if(x){x.src='", $page);
        if(count($pages_nbr)>=2){
        $pages_nbr= explode("';}})();", $pages_nbr[1]);
        $src=($pages_nbr[0]);
        
        
        $content = file_get_contents($src);
        file_put_contents(getcwd().'/photos' . '/'.$time.'.jpg', $content);}}}
        #';}})();


            if (!file_exists(getcwd().'/logos')) {
                mkdir(getcwd().'/logos', 0777, true);
            }
        $div = $DOM->query('//div[@class="publogo"]');
        #define('DIRECTORY', 'C:/xampp/htdocs/big_news/logos');
        foreach($div as $d){
        $image = $d->getElementsByTagName('img');
        if (!empty($image)){
        $id=$d->textContent;
        #x=_ge('emb1283D03AC');if(x){x.src='
            print($id);
            print('<br>');
        
        $content = file_get_contents('https://www.bing.com'.$image[0]->getAttribute('src'));
        if (!file_exists(getcwd().'/logos' . '/'.str_replace(" ","",$d->parentNode->textContent).'.jpg')) {
        file_put_contents(getcwd().'/logos' . '/'.str_replace(" ","",$d->parentNode->textContent).'.jpg', $content);}}}
        print_r($data);
        for ($i=0;$i<count($data['b_title']) ;$i++){
            $query=('INSERT INTO `big_news` (`b_query`, `b_title`, `b_description`, `b_support`, `b_link`, `b_timestamp`) VALUES("'.$data["b_query"][0].'","'.$data["b_title"][$i].'","'.$data["b_description"][$i].'","'.$data["b_support"][$i].'","'.$data["b_link"][$i].'","'.$data["b_timestamp"][$i].'")');
            #print($query);
            $mysqli->query($query);
        }
        
}
else {
    print "Not found";
}


