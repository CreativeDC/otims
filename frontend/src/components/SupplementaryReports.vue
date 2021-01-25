<template>
  <div>
    <div class="container" id="reportsPage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Supplementary Reading Materials (SRMs) Distribution Reports</span>

              <p>Please select a province and click the Download button to download the Supplementary Reading Materials (SRM) distribution datasets/reports for 2019.</p>

              <div class="form-field" style=" margin-top: 10px;">
                <label for="provinceDDL">Province</label>
                <select id="provinceDDL" name="provinceDDL" v-model="selectedProvince">
                  <option value disabled>Select Province</option>
                  <option value="herat">Herat</option>
                  <option value="kabul">Kabul</option>
                  <option value="laghman">Laghman</option>
                  <option value="nangarhar">Nangarhar</option>
                </select>
              </div>

              <button
                class="btn waves-effect light-blue darken-4"
                id="downloadSRM"
                @click="downloadReport"
              >Download</button>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Total SRMs Printed and Distributed by Province in 2019</span>

              <table class="striped">
                <thead>
                  <tr>
                    <th>Province</th>
                    <th>Grade 1</th>
                    <th>Grade 2</th>
                    <th>Grade 3</th>
                    <th>Total</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>Herat</td>
                    <td>204,206</td>
                    <td>189,539</td>
                    <td>187,418</td>
                    <td>581,163</td>
                  </tr>

                  <tr>
                    <td>Kabul</td>
                    <td>47,922</td>
                    <td>44,353</td>
                    <td>40,178</td>
                    <td>132,453</td>
                  </tr>

                  <tr>
                    <td>Laghman</td>
                    <td>16,672</td>
                    <td>18,056</td>
                    <td>12,868</td>
                    <td>47,596</td>
                  </tr>

                  <tr>
                    <td>Nangarhar</td>
                    <td>74,496</td>
                    <td>66,634</td>
                    <td>57,666</td>
                    <td>198,796</td>
                  </tr>

                  <tr>
                    <td>Total</td>
                    <td>343,296</td>
                    <td>318,582</td>
                    <td>298,130</td>
                    <td>960,008</td>
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
  name: "Reports",

  created() {},

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  data() {
    return {
      selectedProvince: "kabul"
    };
  },

  methods: {
    downloadReport: function() {
      //   console.log(this.selectedProvince);

      if (
        this.selectedProvince == "kabul" ||
        this.selectedProvince == "herat"
      ) {
        window.open(
          "http://74.208.121.179/SRM_Printed_and_Distributed_Herat_and_Kabul_2019.xlsx",
          "_blank"
        );
      } else if (
        this.selectedProvince == "nangarhar" ||
        this.selectedProvince == "laghman"
      ) {
        window.open(
          "http://74.208.121.179/SRM_Printed_and_Distributed_Nangarhar_and_Laghman_2019.xlsx",
          "_blank"
        );
      }
    }
  }
};
</script>
              
<style>
</style>