<html>
  <head>
    <title>Format insert set menjadi values</title>
  </head>
  <body>
    <textarea name="data" id="data" style="width:100%;height:200px"></textarea>
    </br>
    </br>
    <button onclick="convert()" id="convert">Convert</button>
    <button onclick="copyText()" style="margin-left:40px;">Copy Text</button>
    </br>
    <div style="width:100%">
      <p id="pre">

      </p>
    </div>
  </body>
  <script>
    var copy="";
    function convert(){
        //contoh data
        var data2=`INSERT INTO db_qc.tbl_tq_temp_random2 SET
          no_item='$_GET[no_item]',
          no_hanger='$rcek[no_hanger]',
          temp_rwick_l2='$_POST[rwick_l2]',
          temp_rwick_w2='$_POST[rwick_w2]',
          temp_rabsor_b1='$_POST[rabsor_b1]',
          temp_rdryaf1='$_POST[rdryaf1]',
          sts='2',
          tgl_buat=CURRENT_TIMESTAMP,
          tgl_update=CURRENT_TIMESTAMP
          `;

        var data = document.getElementById('data').value;
        var pre = document.getElementById('pre');
        var pisahdb = data.split("SET");
        if(pisahdb.length>1){
          var query= pisahdb[1].replace(/ {4}|[\t\n\r]/gm,'');
          var baris = query.split(",");
          var kolom=[];
          var param=[];
          var value=[];
          for (let i = 0; i < baris.length; i++) {
            var dtbaris =baris[i].split("=");
            kolom.push(dtbaris[0].trim());
            param.push("?");
            value.push(dtbaris[1]);
          }
          console.log(kolom);
          var insert= pisahdb[0]+"("+kolom.join(',')+") VALUES ("+value.join(',')+")";
        }else{
          var insert= data;
        }
        console.log(insert);   
        pre.innerHTML =insert;
        copy=insert;
    }
    
    function copyText() {
        convert();
        // const text = copy;
        const textElement = document.getElementById('pre');
        const text = textElement.textContent;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
            }).catch(err => {                
                alert('Text Not copied to clipboard! '+err);
            });
        } else {
            fallbackCopyTextToClipboard(text);
        }
    }
        
    </script>
</html>


                