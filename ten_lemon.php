<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("TEN LEmon");
?>

<div id="wrapper">
    <h4>{{ wow.ten_lemon }}</h4>
    <h3>{{ wow.i }}</h3>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script>
    var app = new Vue({
        el: '#wrapper',
        data: {
            wow: {
                i: 0,
                ten_lemon: 10000000
            },
        },
        mounted() {
            this.send();
        },
        watch:{
          'wow.i':{
              handler: function watch() {
                  if (this.wow.i <= this.wow.ten_lemon) {
                      this.$nextTick(() => {
                          this.send();
                      });
                  }
              },
          }
        },
        methods: {
            async send() {
                fetch('/local/php_interface/lib/Activity.php', {
                    // mode: 'no-cors',
                    method: 'POST',
                    // credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.wow)
                })
                    .then(response => {
                        console.log('response');
                        console.log(response);
                        return response.json()
                    })
                    .then(result => {
                        console.log('result');
                        console.log(result);
                        this.wow.i = result;
                    })
                    .catch(error => {
                        console.log('error');
                        console.log(error);
                    });
            }
        }
    })
</script>