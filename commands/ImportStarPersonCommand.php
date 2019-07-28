<?php

/**
 * #163: загрузка звездных персон из фабрики контента
 * Class ImportStarPersonCommand
 */
class ImportStarPersonCommand extends WomanConsoleCommand
{

  /**
   * панеслася!
   * @param $fileName
   * @param bool $stopOnError
   * @param bool $overwrite
   * @throws Exception
   */
  public function actionIndex($fileName, $stopOnError = true, $overwrite = false)
  {
    $this->setRobotName('StarPersonImporter');

    $this->log('use file name: '.$fileName);
    if(! file_exists($fileName)) throw new Exception('file not exists: '.$fileName);

    $content = file_get_contents($fileName);
    if($content === false) throw new Exception('can not read file: '.$fileName);

    $xml = new SimpleXMLElement($content);
    $personCountImported      = 0;
    $personCountAlreadyExists = 0;
    $personCountError         = 0;

    foreach($xml->articles[0] as $article)
    {
      $this->log('create person...');
      try
      {
        $starPerson = $this->createPerson($article, $xml);
      }
      catch(Exception $e)
      {
        $this->log('exception catched: '.$e->getMessage());
        $personCountError++;
        if($stopOnError) throw $e;
      }
      $this->log('person name: '.$starPerson->getNameRussian().($starPerson->getNameEnglish() ? ' ('.$starPerson->getNameEnglish().')' : ''));

      if($starPerson->isExists())
      {
        if(! $overwrite)
        {
          $this->log('person already exists, skip it');
          $personCountAlreadyExists++;
          continue;
        }

        $this->log('person already exists, remove old db record...');
        Db::execute('delete from {{star_person}} where tag_id = :tag_id', [ ':tag_id' => $starPerson->getTagId(), ]);
      }

      $this->log('upload person image...');
      $this->uploadPersonImage($starPerson);

      $this->log('save person...');
      $starPerson->save();
      $personCountImported++;
    }

    $this->log('done, persons imported: '.$personCountImported.', persons already exists: '.$personCountAlreadyExists.', person count error: '.$personCountError);
  }


  /**
   * загружает картинку на BFS
   * @param StarPerson $starPerson
   * @throws Exception
   */
  private function uploadPersonImage(StarPerson $starPerson, ?string $imageUrl = null)
  {
    $arrImageInfo = $starPerson->getImageInfo();
    $url = isset($arrImageInfo['src']) ? $arrImageInfo['src'] : null;
    if($imageUrl != null) $url = $imageUrl;
    if($url === null)
    {
      $this->log('person has no image, no need for upload');
      return;
    }

    $this->log('download image: '.$url);
    $content = file_get_contents($url);
    if($content === false) throw new Exception('can not download: '.$url);
    $this->log('download done, size: '.number_format(strlen($content)).' bytes');
    $image = imagecreatefromstring($content);
    $name = 'star_'.$starPerson->getTagId();
    if(! is_resource($image)) throw new Exception('can not create image from data');
    $options = array(
        'param_name'	=> $name.'jpg',
        'type'		=> 'starperson',
    );

    $this->log('upload file to BFS...');
    $url = BFSHelper::uploadBinaryData($content, '/images/star/'.$name.'.jpg', '', $url = true);
    $this->log('file uploaded to: '.$url);
    $arrImageInfo['src'] = $url;

    //загрузка файлов в хранилище;
    $tmpfile  = tmpfile();
    fwrite($tmpfile, $content);
    $data = stream_get_meta_data($tmpfile);
    $handler = new UploadHandler($options);
    $files   = $handler->handle_file_upload($data['uri'], $name.'.jpg');
    fclose($tmpfile);
    $arrImageInfo['upload'] = $files->toArray();

    return $starPerson->setImageInfo($arrImageInfo);
  }



  /**
   * создает и возвращает персону на основании xml
   * @param SimpleXMLElement $xmlArticle
   * @param SimpleXMLElement $xmlAll
   * @return StarPerson
   */
  private function createPerson(SimpleXMLElement $xmlArticle, SimpleXMLElement $xmlAll)
  {
    return (new StarPerson())
      ->setTagId($this->getTagIdByUrl((string) $xmlArticle->future_url))
      ->setArticleIdExternal(trim((string) $xmlArticle->id))
      ->setImportedAt(date('Y-m-d H:i:s'))
      ->setPublishedAt(null)
      ->setStatus(StarPerson::STATUS_NOT_CONFIRMED)
      ->setPageH1(trim((string) $xmlArticle->title))
      ->setPageTitle(trim((string) $xmlArticle->page_title))
      ->setPageKeyword($this->getKeyword($xmlArticle->keywords->keyword, $xmlAll))
      ->setPageDescription(trim((string) $xmlArticle->description))
      ->setNameRussian(trim((string) $xmlArticle->additional_fields->Name_Lastname))
      ->setNameEnglish(trim((string) $xmlArticle->additional_fields->Name_Lastname_eng))
      ->setPseudonym(trim((string) $xmlArticle->additional_fields->Pseudonym))
      ->setRole($this->getRole($xmlArticle->additional_fields->Celebrity_role, $xmlAll))
      ->setUserInstagramm(trim((string) $xmlArticle->additional_fields->Insta))
      ->setPseudonymPriority($this->getPseudonymPriority($xmlArticle->additional_fields->Pseudonym_Priority, $xmlAll))
      ->setDateBirthday($this->parseDate($xmlArticle->additional_fields->Birth_date))
      ->setPlaceBirthday(trim((string) $xmlArticle->additional_fields->Birth_place))
      ->setDateDeath($this->parseDate($xmlArticle->additional_fields->Death_date))
      ->setHtmlIntro(trim((string) $xmlArticle->body->page[0]->intro_text))
      ->setImageInfo($this->getImageInfo($xmlArticle->body->page[0]->image))
      ->setSignHoroscope($this->getSignHoroscope($xmlArticle->additional_fields->name, $xmlAll))
      ->setHtmlFull($this->getHtml($xmlArticle->body->page->page_title, $xmlArticle->body->page->simple_html));
  }


  /**
   * @param SimpleXMLElement $xml
   * @return string[]
   */
  private function getImageInfo(SimpleXMLElement $xml)
  {
    if($xml->src == '') return [];

    return
    [
      'src'         => (string) $xml->src,
      'title'       => (string) $xml->title,
      'alt'         => (string) $xml->alt,
      'description' => (string) $xml->description,
      'author'      => (string) $xml->author,
    ];
  }


  /**
   * @param SimpleXMLElement $xmlTitle
   * @param SimpleXMLElement $xmlHtml
   * @return string[]
   */
  private function getHtml(SimpleXMLElement $xmlTitle, SimpleXMLElement $xmlHtml)
  {
    $arrResult =
    [
      'title' => [],
      'html'  => [],
    ];
    foreach($xmlTitle as $xml)
    {
      $arrResult['title'][] = trim((string) $xml);
    }
    foreach($xmlHtml as $xml)
    {
      $html = str_replace('<p></p>', '', (string) $xml);
      $arrResult['html'][] = trim($html);
    }
    return $arrResult;
  }


  /**
   * @param SimpleXMLElement $xmlDate
   * @return string | null
   * @throws Exception
   */
  private function parseDate(SimpleXMLElement $xmlDate)
  {
    $stringDate = trim((string) $xmlDate);
    if(in_array($xmlDate, [ '', '@sashaspilberg Подтвержденный', '13.12', '12.03', ]))  return null;
    if($stringDate == '0711.2016')                                                      return '2016-11-07';
    if($stringDate == '23ю03ю2017')                                                     return '2017-03-23';
    if($stringDate == '-20-12-2009')                                                    return '2009-12-20';
    if(preg_match('#^(\d{4})$#iU', $stringDate, $arrMatch))                             return $arrMatch[1];
    if(preg_match('#(\d{2})\.(\d{2})\.(\d{4})#iU', $stringDate, $arrMatch))             return $arrMatch[3].'-'.$arrMatch[2].'-'.$arrMatch[1];
    if($xmlDate == '.')                                                                 return null;
    if($xmlDate == '23.01.')                                                            return null;
    if($xmlDate == '27.0.1968')                                                         return '1968-01-27';
    if($xmlDate == '30.11')                                                             return null;    //Алексей Полищук дата рождения
    throw new Exception('can not parse date: '.$xmlDate);
  }


  /**
   * @param SimpleXMLElement $xmlKeywordId
   * @param SimpleXMLElement $xmlAll
   * @return string[]
   */
  private function getKeyword(SimpleXMLElement $xmlKeywordId, SimpleXMLElement $xmlAll)
  {
    static $arrList = null;
    if($arrList === null) $arrList = $this->parseList($xmlAll->keywords->keyword);

    $arrKeyword = [];
    foreach($xmlKeywordId as $keywordId)
    {
      $arrKeyword[] = $arrList[(string) $keywordId];
    }
    return $arrKeyword;
  }
  /**
   * @param SimpleXMLElement $xmlRoleId
   * @param SimpleXMLElement $xmlAll
   * @return string
   */
  private function getRole(SimpleXMLElement $xmlRoleId, SimpleXMLElement $xmlAll)
  {
    static $arrList = null;
    if($arrList === null) $arrList = $this->parseList($xmlAll->additional_values->Celebrity_role->item);

    $arrRole = [];
    foreach($xmlRoleId as $roleId)
    {
      $arrRole[] = $arrList[(string) $roleId];
    }
    return $arrRole;
  }
  /**
   * @param SimpleXMLElement $xmlPseudonymPriority
   * @param SimpleXMLElement $xmlAll
   * @return bool | null
   * @throws Exception
   */
  private function getPseudonymPriority(SimpleXMLElement $xmlPseudonymPriority, SimpleXMLElement $xmlAll)
  {
    static $arrList = null;
    if($arrList === null) $arrList = $this->parseList($xmlAll->additional_values->Pseudonym_Priority->item);

    $index = (string) $xmlPseudonymPriority;
    if($index === '') return null;

    $result = $arrList[$index];
    if($result == 'нет') return false;
    if($result == 'да')  return true;
    if($result == '')    return null;
    throw new Exception('unknpwn Pseudonym_Priority value: '.$result);
  }
  /**
   * @param SimpleXMLElement $xmlSignHoroscope
   * @param SimpleXMLElement $xmlAll
   * @return int | null
   * @throws Exception
   */
  private function getSignHoroscope(SimpleXMLElement $xmlSignHoroscope, SimpleXMLElement $xmlAll)
  {
    static $arrList = null;
    if($arrList === null) $arrList = $this->parseList($xmlAll->additional_values->name->item);

    $index = (string) $xmlSignHoroscope;
    if($index === '') return null;

    $arrSign =
    [
      'Овен'     => StarPerson::ARIES,
      'Телец'    => StarPerson::TAURUS,
      'Близнецы' => StarPerson::GEMINI,
      'Рак'      => StarPerson::CANCER,
      'Лев'      => StarPerson::LEO,
      'Дева'     => StarPerson::VIRGO,
      'Весы'     => StarPerson::LIBRA,
      'Скорпион' => StarPerson::SCORPIO,
      'Стрелец'  => StarPerson::SAGITTARIUS,
      'Козерог'  => StarPerson::CAPRICORN,
      'Водолей'  => StarPerson::AQUARIUS,
      'Рыбы'     => StarPerson::PISCES,
    ];
    $sign = $arrList[$index];
    if($sign == '') return null;

    return $arrSign[$sign];
  }


  /**
   * @param SimpleXMLElement $xmlAll
   * @return string[]
   * @throws Exception
   */
  private function parseList(SimpleXMLElement $xmlAll)
  {
    $array = [];
    foreach($xmlAll as $xml)
    {
      $array[(string) $xml->attributes()['id']] = (string) $xml;
    }
    return $array;
  }


  /**
   * @param string $url
   * @return int
   * @throws Exception
   */
  private function getTagIdByUrl($url)
  {
    $url = rtrim($url, '/').'/';
    if(! preg_match('#/articles/tag/stars-encyclopedia/(.+)/$#iU', $url, $arrMatch) &&
        ! preg_match('#/person/show/(.+)/$#iU', $url, $arrMatch)
    )
        throw new Exception('can not parse url: '.$url);
    $webName = trim($arrMatch[1], '/');
    $webName = trim($webName);
    if($webName === 'evgenii_kuzin') $webName = 'evgenii-kuzin';
    if($webName === 'elena-yakovleva') $webName = 'elena-jakovleva';
    if($webName === 'boris-smolkin/') $webName = 'boris-smolkin';


    $result = Db::fetch('
      select min(id) id, count(*) cnt
      from {{tags}}
      where webname = :webname
      group by webname',
      [ ':webname' => $webName ]);
    if(! $result) throw new Exception('can not get tag by webname: '.$webName);
    if($result->cnt != 1) throw new Exception('invalid webname count found: '.$result->cnt);
    return $result->id;
  }

    public function actionWmn1067(String $imageUrl = '')
    {
        if($imageUrl == '') $imageUrl = Yii::app()->params['baseUrl'].'/i/lenalenina.jpg';
        $tag = new Tag();
        $tag->id = 3343407; //лена ленина

        $this->setRobotName('LenaLeninaUpdate');
        $starPerson = StarPerson::getByTagOrNull($tag);

        if($starPerson === null){
            $this->log('persons id = 3343407 do not find');
            return;
        }

        self::uploadPersonImage($starPerson, $imageUrl);

        $role = "Писательница";
        $starPerson->setRole([$role]);

        $placeBirthday = "Сибирь";
        $starPerson->setPlaceBirthday($placeBirthday);

        $htmlIntro = "Яркая писательница, королева высоких причесок и маникюра, Лена Ленина научилась эпатировать фотографов Каннского кинофестиваля своими образами, успела написать 24 книги на русском и французском языке, смогла построить 200 салонов сети студий маникюра Лены Лениной и стать заботливой мамой.";

        $starPerson->setHtmlIntro($htmlIntro);

        $title = [
            "Биография Лены Лениной",
            "Личная жизнь Лены Лениной",
            "Последние новости о Лене Лениной"
        ];

        $html = [
            "<p>Королева эпатажа Лена Ленина, в девичестве Разумова, родилась 25 октября 1979 года в сибирском научном центре в семье ученых-медиков. Отец Алексей изобрел метод лечения онкологии, а мать Людмила опубликовала целый ряд научных статей по кардиологии. С детства Лена была не только красивым, но и целеустремленным ребенком. К тому же она говорила по-французски, потому что учила язык с детского сада, поэтому у нее было несколько прозвищ: Француженка, Модель и Торпеда. Она читала французские книги в оригинале и была любимой ученицей преподавательницы русского языка и литературы. Отлично писала сочинения в школе и даже победила в городском конкурсе декламирования стихов Мориса Карема на французском языке. После школы она поступила в Новосибирский университет, но очень скоро начала сниматься как модель в рекламных роликах, вышла замуж за студента другого факультета и родила сына.</p><p>В возрасте 17 лет у Лены проявились задатки предпринимателя, и она организовала студию видеопроизводства, а в 18 лет она стала самой молодой предпринимательницей в производстве рекламных роликов и телевизионных программ с тремя десятками подчиненных в штате.</p>\n<p>Заработав немного денег спустя несколько лет, она развелась и переехала с мамой и сыном в страну своей мечты — Францию. Там снялась в главной роли в фильме про Иоганна-Себастьяна Баха и стала звездой реалити-шоу первого французского канала TF1 о 12 представителях разных национальностей Nice People.</p><p>В 2003 году она написала свою первую книгу на французском языке. Книга стала бестселлером. После этого ее заметили и на родине, и она заключила контракт с литературным издательством «Эксмо». За десять лет она опубликовала 24 книги на русском и французском языках в таких издательствах, как «Эксмо», «АСТ», «Ардис», Grasset, Floran Massot, Le Rocher и Michel Lafon. Большинство книг написаны об успехе в бизнесе, в личной жизни или в шоу-бизнесе.</p><p>В России Лена стала самым заметным организатором звездных светских вечеринок и лицом более трех десятков рекламных брендов. А в бизнесе стала лидером с двумя сотнями франшиз в сети студий маникюра Лены Лениной.</p><p>Став звездой шоу-бизнеса, Лена начала читать мастер-классы о том, как заработать миллион, о том, как соблазнить любого, о том, как стать знаменитой и сделать из своего имени бренд.</p><p>Лена была главным редактором журналов «Шпилька» и «WomanHit», а сегодня является колумнистом Woman.ru.</p><p>Королева эпатажа Лена Ленина, в девичестве Разумова, родилась 25 октября 1979 года в сибирском научном центре в семье ученых-медиков. Отец Алексей изобрел метод лечения онкологии, а мать Людмила опубликовала целый ряд научных статей по кардиологии. С детства Лена была не только красивым, но и целеустремленным ребенком. К тому же она говорила по-французски, потому что учила язык с детского сада, поэтому у нее было несколько прозвищ: Француженка, Модель и Торпеда. Она читала французские книги в оригинале и была любимой ученицей преподавательницы русского языка и литературы. Отлично писала сочинения в школе и даже победила в городском конкурсе декламирования стихов Мориса Карема на французском языке. После школы она поступила в Новосибирский университет, но очень скоро начала сниматься как модель в рекламных роликах, вышла замуж за студента другого факультета и родила сына.</p><p>В возрасте 17 лет у Лены проявились задатки предпринимателя, и она организовала студию видеопроизводства, а в 18 лет она стала самой молодо0й предпринимательницей в производстве рекламных роликов и телевизионных программ с тремя десятками подчиненных в штате.<\/p>\n<p>Заработав немного денег спустя несколько лет, она развелась и переехала с мамой и сыном в страну своей мечты — Францию. Там снялась в главной роли в фильме про Иоганна-Себастьяна Баха и стала звездой реалити-шоу первого французского канала TF1 о 12 представителях разных национальностей Nice People.</p><p>В 2003 году она написала свою первую книгу на французском языке. Книга стала бестселлером. После этого ее заметили и на родине, и она заключила контракт с литературным издательством «Эксмо». За десять лет она опубликовала 24 книги на русском и французском языках в таких издательствах, как «Эксмо», «АСТ», «Ардис», Grasset, Floran Massot, Le Rocher и Michel Lafon. Большинство книг написаны об успехе в бизнесе, в личной жизни или в шоу-бизнесе.</p><p>В России Лена стала самым заметным организатором звездных светских вечеринок и лицом более трех десятков рекламных брендов. А в бизнесе стала лидером с двумя сотнями франшиз в сети студий маникюра Лены Лениной.</p><p>Став звездой шоу-бизнеса, Лена начала читать мастер-классы о том, как заработать миллион, о том, как соблазнить любого, о том, как стать знаменитой и сделать из своего имени бренд.</p><p>Лена была главным редактором журналов «Шпилька» и «WomanHit», а сегодня является колумнистом Woman.ru.</p>",
            "<p>От первого мужа у Лены есть сын. В 2010 Ленина вышла замуж второй раз, за французского миллионера. Церемония проходила дважды, на Бали и в Москве, а свидетелями были Иосиф Кобзон и ныне покойная ясновидящая Джуна. Спустя несколько лет пара развелась из-за ревности супруга. И сегодня успешная женщина не торопится выходить замуж.</p>",
            "<p>Лена завела себе миниатюрного йорка Леопольду, переехала в Подмосковье из Парижа, где у нее остался большой четырехэтажный дом, а в России приобрела сразу два дома: в поселке Кембридж на Новой Риге и в поселке Марсель на Калужском шоссе. В прошлом году она купила своей маме в подарок дом в Греции на берегу моря. А в этом году начала строительство нового дома в подмосковном гольф-клубе Завидово.</p>"
        ];

        $starPerson->setHtmlFull(['title' => $title, 'html' => $html]);

        Db::execute('delete from {{star_person}} where tag_id = :tag_id', [ ':tag_id' => $tag->id, ]);
        $this->log('save person...');
        $starPerson->save();
    }
    /**
     * изменение картинки для /articles/tag/ani-lorak/
     * @throws Exception
     */
    public function actionWmn1739(string $imageUrl = '')
    {
        $this->setRobotName('AniLrakUpdate');
        if($imageUrl == '') $imageUrl = Yii::app()->params['baseUrl'].'/i/ani-lorak.jpg';

        $tag = new Tag();
        $tag->id = 4912109;

        $starPerson = StarPerson::getByTagOrNull($tag);

        if($starPerson === null){
            $this->log('persons id = 3343407 do not find');
            return;
        }

        self::uploadPersonImage($starPerson, $imageUrl);

        Db::execute('delete from {{star_person}} where tag_id = :tag_id', [ ':tag_id' => $tag->id, ]);
        $this->log('save person...');
        $starPerson->save();
    }

    /**
     * изменение картинки для /articles/tag/rozi-hantington-uajtli/
     * @throws Exception
     */
    public function actionRosieHuntington(string $imageUrl = '')
    {
        $this->setRobotName('command RosieHuntington');
        if($imageUrl == '') $imageUrl = Yii::app()->params['baseUrl'].'/i/ani-lorak.jpg';

        $tag = new Tag();
        $tag->id = 3341071;

        $starPerson = StarPerson::getByTagOrNull($tag);

        if($starPerson === null){
            $this->log('persons id = 3341071 do not find');
            return;
        }

        self::uploadPersonImage($starPerson, $imageUrl);

        Db::execute('delete from {{star_person}} where tag_id = :tag_id', [ ':tag_id' => $tag->id, ]);
        $this->log('save person...');
        $starPerson->save();
    }
};