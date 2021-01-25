<template>
  <div>
    <div class="container" id="balancePage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">{{$t(`text.current_inventory_title`)}}</span>

              <table class="striped centered">
                <thead>
                  <tr>
                    <th>{{$t(`text.subject_title`)}}</th>
                    <th>{{$t(`text.grade`)}}</th>
                    <th>{{$t(`text.language`)}}</th>
                    <th>{{$t(`text.quantity`)}}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="(title, index) in currBalance" v-bind:key="index">
                    <td>{{title.title}}</td>
                    <td>{{title.grade}}</td>
                    <td>{{title.lang}}</td>
                    <td>{{title.total}}</td>
                  </tr>

                  <tr v-show="currBalance.length < 1">
                    <td>{{$t(`text.no_inventory_data`)}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CurrentBalance",

  mounted: function() {
    this.getCurrentBalance();
  },

  data() {
    return {
      currBalance: []
    };
  },

  methods: {
    getCurrentBalance: function() {
      var instance = this;

      this.$http
        .get("/balance/list", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.currBalance = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    }
  }
};
</script>
