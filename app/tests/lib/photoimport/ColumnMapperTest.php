<?php

use Mockery as m;
use lib\photoimport\ods\ColumnMapper;

class ColumnMapperTest extends TestCase {

  protected $mapper;

  public function tearDown()
  {
    m::close();
  }

  public function setUp()
  {
    parent::setUp();
    $this->mapper = App::make('lib\photoimport\ods\ColumnMapper');
  }

  public function testShouldAttachLicenseToAttributes()
  {
    $attributes = array();

    $this->mapper->getPermissions('NO-YES', $attributes);
    $this->assertEquals('NO', $attributes['allowCommercialUses']);
    $this->assertEquals('YES', $attributes['allowModifications']);

    $this->mapper->getPermissions('YES, NO', $attributes);
    $this->assertEquals('YES', $attributes['allowCommercialUses']);
    $this->assertEquals('NO', $attributes['allowModifications']);
  }

  public function testShouldAttachTagstoAttributes() {
    $attributes = array();
    $attrs = array(
        'tags_elementos' => 'janela, porta',
        'tags_materiais' => 'tijolo, vidro',
        'tags_tipologia' => 'religioso, igreja',
      );
    $this->mapper->getTags($attrs, $attributes);
    $this->assertEquals('janela, porta, tijolo, vidro, religioso, igreja', $attributes['tags']);

    $attrs = array(
        'tags_elementos' => 'janela, porta',
        'tags_materiais' => '',
        'tags_tipologia' => 'religioso, igreja',
      );
    $this->mapper->getTags($attrs, $attributes);
    $this->assertEquals('janela, porta, religioso, igreja', $attributes['tags']);

    $attrs = array(
        'tags_elementos' => '',
        'tags_materiais' => 'tijolo, vidro',
        'tags_tipologia' => 'religioso, igreja',
      );
    $this->mapper->getTags($attrs, $attributes);
    $this->assertEquals('tijolo, vidro, religioso, igreja', $attributes['tags']);

    $attrs = array(
        'tags_elementos' => '',
        'tags_materiais' => '',
        'tags_tipologia' => '',
      );
    $this->mapper->getTags($attrs, $attributes);
    $this->assertEquals('', $attributes['tags']);

  }

  public function testTransformShouldMapColumnsToNewArray()
  {
    $column_mapper = $this->mapper->getMapper();
    $attrs = array(
      'tombo' => 3828,
      'caracterizacao' => '9A812',
      'nome' => 'Igreja de São Francisco de Assis (Igreja da Pampulha)',
      'pais' => 'Brasil',
      'estado' => 'MG',
      'cidade' => 'Belo Horizonte',
      'bairro' => 'Pampulha',
      'rua' => 'Avenida Otacílio Negrão de Lima, 3000',
      'colecao' => 'Catálogo Geral',
      'autor_da_imagem' => 'TOLEDO, Benedito Lima de',
      'data_da_imagem' => '1944/1963',
      'autor_da_obra' => 'NIEMEYER, Oscar Ribeiro de Almeida de',
      'data_da_obra' => '1943/1944',
      'licenca' => 'NO-YES',
      'descricao' => 'Painel do Batistério da Igreja de São Francisco de Assis',
      'tags_materiais' => null,
      'tags_elementos' => 'Mural',
      'tags_tipologia' => 'Religioso, Igreja',
      'observacoes' => 'Amarelada',
      'data_de_tombo' => '1963-03-20',
      'data_de_catalogacao' => '2012-04-09'
    );
    $attributes = $this->mapper->transform($attrs);
    foreach($column_mapper as $mapped_column => $column) {
      $this->assertEquals($attributes[$mapped_column], $attrs[$column]);
    }
  }

}