<?php
// Your code here!

class News {
    
    private $title;
    private $url;
    private $post_date;
    
    function __construct($title, $url, $post_date) {
        $this->title = $title;
        $this->url = $url;
        $this->post_date = $post_date;
    }
    
    function get_title() {
        return $this->title;
    }
    
    function get_url() {
        return $this->url;
    }  
    
    function get_post_date() {
        return $this->post_date;
    }      
    
}


interface NewsBuilder {
    function parse($url);
}


class RssV2NewsBuilder implements NewsBuilder {
    
    function parse($url) {
        
        $xml_data = simplexml_load_file($url);
        
        $news_list = array();
        foreach ( $xml_data->channel->item as $item ) {
            $news_list[] = new News($item->title, $item->link, $item->pubDate);
        }
        
        return $news_list;
        
    }
}

class RssV1NewsBuilder implements NewsBuilder {
    
    function parse($url) {
        
        $xml_data = simplexml_load_file($url);
        
        $news_list = array();
        foreach ( $xml_data->item as $item ) {
            $news_list[] = new News($item->title, $item->link, $item->children("http://purl.org/dc/elements/1.1/")->date);
        }
        
        return $news_list;
        
    }
}

class NewsDirector {
    private $builder;
    private $url;
    
    public function __construct( NewsBuilder $builder, $url ) {
        $this->builder = $builder;
        $this->url = $url;
    }
    
    public function get_news() {
        return $this->builder->parse($this->url);
    }
    
}


// RSS V1
// $director = new NewsDirector( new RssV1NewsBuilder(), "" );

// RSS V2
$director = new NewsDirector( new RssV2NewsBuilder(), "https://www.vektor-inc.co.jp/feed/?post_type=info" );

?>

<html>
    <body>
        <ul>
            <?php foreach ($director->get_news() as $news): ?>
                <li><a href="<?php echo $news->get_url(); ?>"><?php echo $news->get_title(); ?></a>(<?php echo $news->get_post_date(); ?>)</li>
            <?php endforeach; ?>
        </ul>
    </body>
    
</html>
