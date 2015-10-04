<div id="{{ $date_field . '_container' }}" class="six columns date_container">
  <div class="six columns">
    <p>   
      <label>Século:</label>
      <select id="century" name="century" class="date">
        <option value="NS">Escolha o Século</option>
        <option value="Before">Antes do Século XV</option>
        <option value="XV">Século XV</option>
        <option value="XVI">Século XVI</option>
        <option value="XVII">Século XVII</option>
        <option value="XVIII">Século XVIII</option>
        <option value="XIX">Século XIX</option>
        <option value="XX">Século XX</option>
        <option value="XXI">Século XXI</option>
      </select>
           
      <span id="period_select" name="period_select"></span>
      <!--<span id="period_select2" name="period_select2"></span>
      <select id="period_select" name="period_select" placeholder="Ano">        
      </select>-->
      <br>
      <br>
      <label>Decada:</label>
      <select id="decade_select" name="decade_select" placeholder="Decada">
            <option >Escolha a Década</option>
            <option value="1501 a 1510">1501 a 1510</option>
            <option value="1511 a 1520">1511 a 1520</option>
            <option value="1521 a 1530">1521 a 1530</option>
      </select>      
        <br>

      <!--<span>
      Você sabe o mês específico de conclusão da obra? <br>
      </span>
      <label>Mês de conclusão :</label>
      <select id="month" name="month" class="date">
        <option >Sélecione o Mês</option>
        <option value="janeiro">Janeiro</option>
        <option value="fevereiro">Fevereiro</option>
        <option value="marco">Março</option>
        <option value="abril">Abril</option>        
      </select>
      <br>
      <br>
      <span>
        Você sabe qual foi o dia de conclusão da obra? <br>
      </span>
      <label>Dia de conclusão:</label>
      <select id="day_select" name="day_select" placeholder="Dia"></select>
      <br><br><br>-->
      <!--
      Seleciona o formato:
      <select class="date">
        <option value="day">dia/mês/ano</option>
        <option value="month">mês/ano</option>
        <option value="year">ano</option>
        <option value="decade">década</option>
        <option value="century">século</option>
      </select>
      <br>


      <label>Intervalo:</label>
      <input type="radio" name="{{ $date_field }}_radio" class="date_interval" value="" checked>
      <label>Uma única data</label>
      <input type="radio" name="{{ $date_field }}_radio" class="date_interval" value="interval">
      <label>Intervalo entre duas datas</label> -->
    </p>
  </div>
  <!--<div class="six columns date_content">
    <div class="date_box">
      <input type="text" name="{{ $date_field }}1" class="day"
        placeholder="Ex.: {{ date('d/m/Y') }}" />
      <p class="date_translation"></p>
    </div>
    <div class="one column" style="width: 15px; text-align: center;"><p class="interval_text"></p></div>
    <div class="date_box"></div>
  </div>-->
</div>