@extends('layouts.default')
@section('head')
	<title>Arquigrafia - Fotos - Upload</title>
	<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
	<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
@stop
@section('content')
	<script type="text/javascript">
		$( window ).load(function() {
			$("#preview_photo").hide();
		});
	</script>
	<div class="container">
		<div>
			<!--{{ Form::open(array('url'=>'photos', 'files'=> true)) }}-->
			{{ Form::open(array('url' => "photos/savePhotoInstitutional", 'files'=> true)) }}
				<div class="twelve columns row step-1">
					<h1><span class="step-text">Upload</span></h1>
					<div class="four columns alpha">
						<img src="" id="preview_photo">
						<p>
							{{ Form::label('photo','Imagem:') }}
							{{ Form::file('photo', array('id'=>'imageUpload', 'onchange' => 'readURL(this);')) }}
							<div class="error">{{ $errors->first('photo') }}</div>
						</p>
						<br>
					</div>
				</div>
				<div id="registration" class="twelve columns row step-2">
					<h1><span class="step-text">Dados da imagem</span></h1>
					<p>(*) Campos obrigatórios.</p>
					<p>{{ Form::hidden('pageSource', $pageSource) }} </p>

					<br>
					<div class="eight columns alpha row">
						<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
							@if(Session::get('institutionId'))
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('support', 'Suporte*:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('support', Input::old('support')) }} <br>
											<div class="error">{{ $errors->first('support') }}</div>
										</p>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('tomboTxt', 'Tombo*:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('tombo', Input::old('tombo')) }} <br>
											<div class="error">{{ $errors->first('tombo') }}</div>
										</p>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('subjectTxt', 'Assunto*:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('subject', Input::old('subject')) }} <br>
											<div class="error">{{ $errors->first('subject') }}</div>
										</p>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('hygieneDateTxt', 'Data de higienização:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('hygieneDate', Input::old('hygieneDate')) }} <br>
											<div class="error">{{ $errors->first('hygieneDate') }}</div>
										</p>
									</div>
								</td>
							</tr>
														
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('backupDateTxt', 'Data de backup:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('backupDate', Input::old('backupDate')) }} <br>
											<div class="error">{{ $errors->first('backupDate') }}</div>
										</p>
									</div>
								</td>
							</tr>

							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('characterizationTxt', 'Caracterização*:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('characterization', Input::old('characterization')) }} <br>
											<div class="error">{{ $errors->first('characterization') }}</div>
										</p>
									</div>
								</td>
							</tr>

							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('cataloguingTimeTxt', 'Data de Catalogação:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('cataloguingTime', Input::old('cataloguingTime')) }} <br>
											<div class="error">{{ $errors->first('cataloguingTime') }}</div>
										</p>
									</div>
								</td>
							</tr>

							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('userResponsibleTxt', 'Usuário Responsável:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('userResponsible', Input::old('userResponsible')) }} <br>
											<div class="error">{{ $errors->first('userResponsible') }}</div>
										</p>
									</div>
								</td>
							</tr>
							@endif
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('photo_name', 'Título*:') }}</p>
									</div>
									<div class="three columns omega">
										<p>{{ Form::text('photo_name', Input::old('photo_name')) }} <br>
											<div class="error">{{ $errors->first('photo_name') }}</div>
										</p>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha">
										<p>{{ Form::label('photo_description', 'Descrição:') }}</p>
									</div>
									<div class="three columns omega">
										<p>
											{{ Form::textarea('photo_description', Input::old('photo_description')) }}<br>
										</p>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha"><p>{{ Form::label('tagsMaterial_input', 'Tags de materiais*:') }}</p></div>
									<div class="five columns">
										<p>
											{{ Form::text('tagsMaterial_input') }}
											<button class="btn" id="add_tag_material" style="font-size: 11px;">ADICIONAR TAG DE MATERIAIS</button>
											<br>
											<div class="error">{{ $errors->first('tagsMaterial') }}</div>
										</p>
									</div>
									<div class="five columns alpha">
										<textarea name="tagsMaterial" id="tagsMaterial" cols="60" rows="1" style="display: none;"></textarea>
									</div>
									
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha"><p>{{ Form::label('tagsElements_input', 'Tags de elementos*:') }}</p></div>
									<div class="five columns">
										<p>
											{{ Form::text('tagsElements_input') }}
											<button class="btn" id="add_tag_elements" style="font-size: 11px;">ADICIONAR TAG DE ELEMENTOS</button>
											<br>
											<div class="error">{{ $errors->first('tagsElements') }}</div>
										</p>
									</div>
									<div class="five columns alpha">
										<textarea name="tagsElements" id="tagsElements" cols="60" rows="1" style="display: none;"></textarea>
									</div>
									
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha"><p>{{ Form::label('tagsTypology_input', 'Tags de tipologia*:') }}</p></div>
									<div class="five columns">
										<p>
											{{ Form::text('tagsTypology_input') }}
											<button class="btn" id="add_tag_typology" style="font-size: 11px;">ADICIONAR TAG DE TIPOLOGIA</button>
											<br>
											<div class="error">{{ $errors->first('tagsTypology') }}</div>
										</p>
									</div>
									<div class="five columns alpha">
										<textarea name="tagsTypology" id="tagsTypology" cols="60" rows="1" style="display: none;"></textarea>
									</div>
									
								</td>
							</tr>
							<tr>
								<td>
									<div class="two columns alpha"><p>{{ Form::label('tags_input', 'Tags*:') }}</p></div>
									<div class="five columns">
										<p>
											{{ Form::text('tags_input') }}
											<button class="btn" id="add_tag" style="font-size: 11px;">ADICIONAR TAG</button>
											<br>
											<div class="error">{{ $errors->first('tags') }}</div>
										</p>
									</div>
									<div class="five columns alpha">
										<textarea name="tags" id="tags" cols="60" rows="1" style="display: none;"></textarea>
									</div>
									
								</td>
							</tr>

							<tr>
								<td>
								<div class="two columns alpha"><p>{{ Form::label('photo_workAuthor', 'Autor da obra:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::text('photo_workAuthor', Input::old('photo_workAuthor')) }} <br>
									</p>
								</div>
								</td>
							</tr>

							<tr>  <td>              
         						<div class="two columns alpha"><p>{{ Form::label('photo_workDate', 'Data da obra:') }}</p></div>
         						<div class="two columns omega">         						
          						<p>
          							{{ Form::text('photo_workDate','',array('id' => 'datePickerWorkDate','placeholder'=>'DD/MM/AAAA')) }} 
         						<br>
         						<div class="error">{{ $errors->first('photo_workDate') }}</div>
         					</p>       
        					</div></td>
        					</tr>

							
							
						</table>
					</div>
					<br class="clear">
					<div class="five columns alpha row">
						<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_country', 'País*:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::select('photo_country', [ "Afeganistão"=>"Afeganistão", "África do Sul"=>"África do Sul", "Albânia"=>"Albânia", "Alemanha"=>"Alemanha", "América Samoa"=>"América Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Arábia Saudita"=>"Arábia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "Bélgica"=>"Bélgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bolívia"=>"Bolívia", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulgária"=>"Bulgária", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canadá"=>"Canadá", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Colômbia"=>"Colômbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados Árabes"=>"Emirados Árabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finlândia"=>"Finlândia", "França"=>"França", "Gabão"=>"Gabão", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Grécia"=>"Grécia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "Índia"=>"Índia", "Indonésia"=>"Indonésia", "Inglaterra"=>"Inglaterra", "Irã"=>"Irã", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Islândia"=>"Islândia", "Israel"=>"Israel", "Itália"=>"Itália", "Iugoslávia"=>"Iugoslávia", "Jamaica"=>"Jamaica", "Japão"=>"Japão", "Jersey"=>"Jersey", "Kirgizstão"=>"Kirgizstão", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "Líbano"=>"Líbano", "Líbia"=>"Líbia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Mauritânia"=>"Mauritânia", "Mauritius"=>"Mauritius", "México"=>"México", "Moçambique"=>"Moçambique", "Mônaco"=>"Mônaco", "Mongólia"=>"Mongólia", "Namíbia"=>"Namíbia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicarágua"=>"Nicarágua", "Nigéria"=>"Nigéria", "Noruega"=>"Noruega", "Nova Zelândia"=>"Nova Zelândia", "Omã"=>"Omã", "Panamá"=>"Panamá", "Paquistão"=>"Paquistão", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polinésia Francesa"=>"Polinésia Francesa", "Polônia"=>"Polônia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Quênia"=>"Quênia", "República Dominicana"=>"República Dominicana", "Romênia"=>"Romênia", "Rússia"=>"Rússia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa Lúcia"=>"Santa Lúcia", "São Vicente"=>"São Vicente", "Singapura"=>"Singapura", "Síria"=>"Síria", "Spiemich"=>"Spiemich", "Sudão"=>"Sudão", "Suécia"=>"Suécia", "Suiça"=>"Suiça", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tailândia"=>"Tailândia", "Taiwan"=>"Taiwan", "Tchecoslováquia"=>"Tchecoslováquia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Yémen"=>"Yémen", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"], "Brasil") }}<br>
										<div class="error">{{ $errors->first('photo_country') }}</div>
									</p>
								</div>
							</tr>
							<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_state', 'Estado:') }}</p></div>
								<div class="two columns omega">
								<p>
									{{ Form::select('photo_state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá", "BA"=>"Bahia", "CE"=>"Ceará", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goiás", "MA"=>"Maranhão", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Pará", "PB"=>"Paraíba", "PE"=>"Pernambuco", "PI"=>"Piauí", "PR"=>"Paraná", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rondônia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"São Paulo", "TO"=>"Tocantins"], "") }} <br>
									<div class="error">{{ $errors->first('photo_state') }}</div>
								</p>
							</tr>
							<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_city', 'Cidade:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::text('photo_city', Input::old('photo_city')) }} <br>
										<div class="error">{{ $errors->first('photo_city') }}</div>
									</p>
								</div>
							</tr>
							
							<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_street', 'Endereço:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::text('photo_street', Input::old('photo_street')) }} <br>
									</p>
								</div>
							</tr>

							<tr>
								
									<div class="two columns alpha"><p>{{ Form::label('photo_imageAuthor', 'Autor da imagem*:') }}</p></div>
									<div class="two columns omega">
										<p>
											{{ Form::text('photo_imageAuthor', $user->name) }} 
											 <br>
											<div class="error">{{ $errors->first('photo_imageAuthor') }}</div>
										</p>
									</div>
								
							</tr>
							<tr>                
         						<div class="two columns alpha"><p>{{ Form::label('photo_imageDate', 'Data da imagem:') }}</p></div>
         						<div class="two columns omega">
          						   <p>{{ Form::text('photo_imageDate','',array('id' => 'datePickerImageDate','placeholder'=>'DD/MM/AAAA')) }} 
         							<br> <div class="error">{{ $errors->first('photo_imageDate') }}</div>
         							</p>       
        							</div>
        						</tr>   
        					
							<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_observation', 'Observações:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::textarea('photo_observation', Input::old('photo_observation')) }} <br>

									</p>
								</div>
							</tr>

							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</div>
					<div class="five columns omega row">
						<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
							<!--<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_imageDate', 'Data da imagem:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::text('photo_imageDate', Input::old('photo_imageDate')) }} <br>
									</p>
								</div>
							</tr> -->
							 
								
							
							<!--
							<tr>
								<div class="two columns alpha"><p>{{ Form::label('photo_workDate', 'Data da obra:') }}</p></div>
								<div class="two columns omega">
									<p>
										{{ Form::text('photo_workDate', Input::old('photo_workDate')) }} <br>
									</p>
								</div>
							</tr> -->

							




							
						</table>
					</div>
					@if(!Session::get('institutionId'))
					<div class="twelve columns omega row">
						<div class="form-group">
							*{{ Form::checkbox('photo_authorization_checkbox', 1, true) }}
							{{ Form::label('photo_authorization_checkbox', '&nbsp;Sou o autor da imagem ou possuo permissão expressa do autor para disponibilizá-la no Arquigrafia')}}
							<br><div class="error">{{ $errors->first('photo_authorization_checkbox') }}</div>
						</div>
					</div>
					@endif
					<div class="twelve columns omega row">
						<label for="terms" generated="true" class="error" style="display: inline-block; "></label>	
						Escolho a licença <a href="http://creativecommons.org/licenses/?lang=pt_BR" id="creative_commons" target="_blank" style="text-decoration:underline; line-height:16px;">Creative Commons</a>, para publicar a imagem, com as seguintes permissões:			
					</div>

					<div class="four columns" id="creative_commons_left_form">
						Permitir o uso comercial da imagem?
						<br>
						 <div class="form-row">
							<input type="radio" name="photo_allowCommercialUses" value="YES" id="photo_allowCommercialUses" checked="checked">
							<label for="photo_allowCommercialUses">Sim</label><br class="clear">
						 </div>
						 <div class="form-row">
							<input type="radio" name="photo_allowCommercialUses" value="NO" id="photo_allowCommercialUses">
							<label for="photo_allowCommercialUses">Não</label><br class="clear">
						 </div>
					</div>
					<div class="four columns" id="creative_commons_right_form">
						Permitir modificações em sua imagem?
						<br>
						<div class="form-row">
							<input type="radio" name="photo_allowModifications" value="YES" id="photo_allowModifications" checked="checked">
							<label for="question_3-5">Sim</label><br class="clear">
						</div>
						<div class="form-row">
							<input type="radio" name="photo_allowModifications" value="YES_SA" id="photo_allowModifications">
							<label for="question_3-5">Sim, contanto que os outros compartilhem de forma semelhante</label><br class="clear">
						</div>
						<div class="form-row">
							<input type="radio" name="photo_allowModifications" value="NO" id="photo_allowModifications">
							<label for="question_3-5">Não</label><br class="clear">
						</div>
					</div>
					<div class="twelve columns">
						<input name="enviar" type="submit" class="btn" value="ENVIAR">
					</div>
				</div>
			{{ Form::close() }}

		</div>

	</div>

	<script type="text/javascript">
		function readURL(input) {
				$("#preview_photo").hide();
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
						$('#preview_photo')
							.attr('src', e.target.result)
							.width(600);
							$("#preview_photo").show();
					};
					reader.readAsDataURL(input.files[0]);
				}
			}

		$(document).ready(function() {
			$('#tags').textext({ plugins: 'tags' });
			$('#tagsMaterial').textext({ plugins: 'tags' });
			$('#tagsElements').textext({ plugins: 'tags' });
			$('#tagsTypology').textext({ plugins: 'tags' });

			@if (isset($tags))
				@foreach ( $tags as $tag )
					$('#tags').textext()[0].tags().addTags([ {{ '"' . $tag . '"' }} ]);
				@endforeach
			@endif


			@if (isset($tagsMaterial))
				@foreach ( $tagsMaterial as $tagMaterial )
					$('#tagsMaterial').textext()[0].tags().addTags([ {{ '"' . $tagMaterial . '"' }} ]);
				@endforeach
			@endif

			@if (isset($tagsElements))
				@foreach ( $tagsElements as $tagElements )
					$('#tagsElements').textext()[0].tags().addTags([ {{ '"' . $tagElements . '"' }} ]);
				@endforeach
			@endif

			@if (isset($tagsTypology))
				@foreach ( $tagsTypology as $tagTypology )
					$('#tagsTypology').textext()[0].tags().addTags([ {{ '"' . $tagTypology . '"' }} ]);
				@endforeach
			@endif


			$('#add_tag').click(function(e) {
				e.preventDefault();
				var tag = $('#tags_input').val();
				if (tag == '') return;
				$('#tags').textext()[0].tags().addTags([ tag ]);
				$('#tags_input').val('');
			});

			$('#add_tag_material').click(function(e) {
				e.preventDefault();				
				var tagMaterial = $('#tagsMaterial_input').val();
				
				if (tagMaterial == '') return;
				$('#tagsMaterial').textext()[0].tags().addTags([ tagMaterial ]);
				$('#tagsMaterial_input').val('');
			});

			$('#add_tag_elements').click(function(e) {
				e.preventDefault();
				var tagElements = $('#tagsElements_input').val();
				if (tagElements == '') return;
				$('#tagsElements').textext()[0].tags().addTags([ tagElements ]);
				$('#tagsElements_input').val('');
			});

			$('#add_tag_typology').click(function(e) {
				e.preventDefault();
				var tagTypology = $('#tagsTypology_input').val();
				
				if (tagTypology == '') return;
				$('#tagsTypology').textext()[0].tags().addTags([ tagTypology ]);
				$('#tagsTypology_input').val('');
			});

			$('#tags_input').keypress(function(e) {
				var key = e.which || e.keyCode;
				if (key == 44 || key == 46 || key == 59) // key = , ou Key = . ou key = ;
					e.preventDefault();
			});
			$('#tagsMaterial_input').keypress(function(e) {
				var key = e.which || e.keyCode;
				if (key == 44 || key == 46 || key == 59) 
					e.preventDefault();
			});
			$('#tagsElements_input').keypress(function(e) {
				var key = e.which || e.keyCode;
				if (key == 44 || key == 46 || key == 59) 
					e.preventDefault();
			});
			$('#tagsTypology_input').keypress(function(e) {
				var key = e.which || e.keyCode;
				if (key == 44 || key == 46 || key == 59) // key = , ou Key = . ou key = ;
					e.preventDefault();
			});
			
			  

		});

		//msy
		$(function() {
    	$( "#datePickerWorkDate" ).datepicker({
      		dateFormat:'dd/mm/yy'
    	}
      	);
    	$( "#datePickerImageDate" ).datepicker({
      	dateFormat:'dd/mm/yy'
    	}
      	);
    	});
			
	</script>

@stop