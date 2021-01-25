<template>
  <div>
    <div class="container" id="reportsPage" style="width: 100%">
      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%">
            <div class="card-content">
              <span class="card-title">Distribution Reports</span>

              <div class="form-field" style="margin-bottom: 30px">
                <label for="reportTypeDDL">Display Type</label>
                <select
                  id="reportTypeDDL"
                  name="reportTypeDDL"
                  v-model="selectedReportType"
                >
                  <option value disabled>Select Report Display Type</option>
                  <option value="visual">Visualized Report</option>
                  <option value="tabular">Tabular Report</option>
                </select>
              </div>

              <div v-if="selectedReportType == 'visual'">
                <div class="form-field">
                  <label for="visualProvinceDDL">Province</label>
                  <select
                    id="visualProvinceDDL"
                    name="visualProvinceDDL"
                    v-model="selectedProvinceId"
                  >
                    <option value disabled>Select Province</option>
                    <option
                      v-for="(province, index) in allProvinces"
                      v-bind:key="index"
                      :value="province.id"
                    >
                      {{ province.en_name }}
                    </option>
                  </select>
                </div>
              </div>

              <div v-if="selectedReportType == 'tabular'">
                <div class="form-field">
                  <label for="dataTypeDDL">Report Type</label>
                  <select
                    id="dataTypeDDL"
                    name="dataTypeDDL"
                    v-model="selectedDataType"
                  >
                    <option value disabled>Select Report Type</option>
                    <option value="allprovinces">All Provinces</option>
                    <option value="province">Selected Province</option>
                    <option value="provincedistricts">
                      Selected Province Districts
                    </option>
                    <option value="district">Selected District</option>
                    <option value="districtschools">
                      Selected District Schools
                    </option>
                  </select>
                </div>

                <div class="row" v-if="showFor(selectedDataType)">
                  <div class="col s11 offset-s1">
                    <div class="card">
                      <div class="card-content">
                        <div class="form-field">
                          <label for="levelProvinceDDL">Province</label>
                          <select
                            id="levelProvinceDDL"
                            name="levelProvinceDDL"
                            v-model="selectedProvinceId"
                          >
                            <option value disabled>Select Province</option>
                            <option
                              v-for="(province, index) in allProvinces"
                              v-bind:key="index"
                              :value="province.id"
                            >
                              {{ province.en_name }}
                            </option>
                          </select>
                        </div>

                        <div
                          class="form-field"
                          v-if="
                            selectedDataType == 'district' ||
                            selectedDataType == 'districtschools'
                          "
                        >
                          <label for="levelDistrictDDL">District</label>
                          <select
                            id="levelDistrictDDL"
                            name="levelDistrictDDL"
                            v-model="selectedDistrictId"
                          >
                            <option value disabled>Select District</option>
                            <option
                              v-for="(district,
                              index) in selectedProvinceDistricts"
                              v-bind:key="index"
                              :value="district.id"
                            >
                              {{ district.en_name }}
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-field" v-show="selectedReportType == 'tabular'">
                <label for="projectDDL">Project</label>
                <select
                  id="projectDDL"
                  name="projectDDL"
                  v-model="selectedProjectId"
                >
                  <option value="0">All Projects</option>
                  <option
                    v-for="project in allProjects"
                    v-bind:key="project.id"
                    :value="project.id"
                  >
                    {{ project.title }}
                  </option>
                </select>
              </div>

              <div class="row">
                <div class="form-field col s6">
                  <label for="startDatePicker">Start Date</label>

                  <local-date-picker
                    id="startDatePicker"
                    name="startDatePicker"
                    class="validate"
                    required
                    aria-required="true"
                    :placeholder="$t(`text.select_date`)"
                    v-model="startDate"
                    format="YYYY/M/D"
                    displayFormat="jYYYY/jM/jD"
                    locale="fa,en"
                    v-bind:locale-config="{
                      fa: {
                        lang: {
                          label: 'لمريز / شمسی',
                          submit: 'ټاکل/تایید',
                          cancel: 'بندول/انصراف',
                          now: 'اوس/اکنون',
                          nextMonth: 'راتلونکې مياشت/ماه بعد',
                          prevMonth: 'تېره مياشت/ماه قبل',
                        },
                      },
                      en: {
                        lang: {
                          label: 'Gregorian / میلادی',
                        },
                      },
                    }"
                  ></local-date-picker>
                </div>
                <div class="form-field col s6">
                  <label for="endDatePicker">End Date</label>

                  <local-date-picker
                    id="endDatePicker"
                    name="endDatePicker"
                    class="validate"
                    required
                    aria-required="true"
                    :placeholder="$t(`text.select_date`)"
                    v-model="endDate"
                    format="YYYY/M/D"
                    displayFormat="jYYYY/jM/jD"
                    locale="fa,en"
                    v-bind:locale-config="{
                      fa: {
                        lang: {
                          label: 'لمريز / شمسی',
                          submit: 'ټاکل/تایید',
                          cancel: 'بندول/انصراف',
                          now: 'اوس/اکنون',
                          nextMonth: 'راتلونکې مياشت/ماه بعد',
                          prevMonth: 'تېره مياشت/ماه قبل',
                        },
                      },
                      en: {
                        lang: {
                          label: 'Gregorian / میلادی',
                        },
                      },
                    }"
                  ></local-date-picker>
                </div>
              </div>

              <a
                class="btn waves-effect light-blue darken-4"
                @click="getReports"
                >Get Reports</a
              >

              <!-- <a
                class="btn waves-effect modal-trigger"
                href="#modalDownloadDatasets"
              >Download Datasets</a>-->
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="selectedReportType == 'tabular'">
        <div class="col s12">
          <div class="card left-align" style="width: 100%">
            <div class="card-content">
              <span class="card-title">Books Inventory & Distribution</span>

              <a id="dlink" style="display: none"></a>

              <input
                type="button"
                class="btn waves-effect light-blue darken-4"
                @click="printTable('dataTable')"
                value="Print"
              />

              <input
                type="button"
                class="btn waves-effect light-blue darken-4"
                @click="tableToExcel('dataTable', 'Data', 'otims_exported.xls')"
                value="Export to Excel"
              />

              <table
                id="dataTable"
                class="striped"
                v-if="selectedDataType == 'allprovinces'"
              >
                <thead>
                  <tr>
                    <th>S. No</th>
                    <th>Province</th>
                    <th>Grade</th>
                    <th>Language</th>
                    <th>Total Sent</th>
                    <th>Total Received</th>
                    <th>Total Damages</th>
                    <th>Total Losts</th>
                    <th>Total Distributed</th>
                    <th>Current Balance</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="(record, index) in allProvincesTabularRecords"
                    v-bind:key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ record.Province }}</td>
                    <td>{{ record.Grade }}</td>
                    <td>{{ record.Lang }}</td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalSent == null ? 0 : record.TotalSent
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalReceived == null
                            ? 0
                            : record.TotalReceived
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDamages == null ? 0 : record.TotalDamages
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalLosts == null ? 0 : record.TotalLosts
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDistributed == null
                            ? 0
                            : record.TotalDistributed
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.RemainingBalance == null
                            ? 0
                            : record.RemainingBalance
                        )
                      }}
                    </td>
                  </tr>
                  <tr style="font-weight: bold; background-color: #e2e2e2">
                    <td></td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(allProvincesTabularRecords, "Province")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(allProvincesTabularRecords, "Grade")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(allProvincesTabularRecords, "Lang")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(allProvincesTabularRecords, "TotalSent")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(allProvincesTabularRecords, "TotalReceived")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(allProvincesTabularRecords, "TotalDamages")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(allProvincesTabularRecords, "TotalLosts")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(allProvincesTabularRecords, "TotalDistributed")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(allProvincesTabularRecords, "RemainingBalance")
                        )
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>

              <table
                id="dataTable"
                class="striped"
                v-if="selectedDataType == 'province'"
              >
                <thead>
                  <tr>
                    <th>S. No</th>
                    <th>Province</th>
                    <th>Grade</th>
                    <th>Title</th>
                    <th>Language</th>
                    <th>Total Sent</th>
                    <th>Total Received</th>
                    <th>Total Damages</th>
                    <th>Total Losts</th>
                    <th>Total Distributed</th>
                    <th>Current Balance</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="(record, index) in provinceTabularRecords"
                    v-bind:key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ record.Province }}</td>
                    <td>{{ record.Grade }}</td>
                    <td>{{ record.Title }}</td>
                    <td>{{ record.Lang }}</td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalSent == null ? 0 : record.TotalSent
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalReceived == null
                            ? 0
                            : record.TotalReceived
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDamages == null ? 0 : record.TotalDamages
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalLosts == null ? 0 : record.TotalLosts
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDistributed == null
                            ? 0
                            : record.TotalDistributed
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.RemainingBalance == null
                            ? 0
                            : record.RemainingBalance
                        )
                      }}
                    </td>
                  </tr>
                  <tr style="font-weight: bold; background-color: #e2e2e2">
                    <td></td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(provinceTabularRecords, "Province")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(provinceTabularRecords, "Grade")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(provinceTabularRecords, "Title")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(provinceTabularRecords, "Lang")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceTabularRecords, "TotalSent")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceTabularRecords, "TotalReceived")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceTabularRecords, "TotalDamages")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceTabularRecords, "TotalLosts")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceTabularRecords, "TotalDistributed")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceTabularRecords, "RemainingBalance")
                        )
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>

              <table
                id="dataTable"
                class="striped"
                v-if="
                  selectedDataType == 'provincedistricts' ||
                  selectedDataType == 'district'
                "
              >
                <thead>
                  <tr>
                    <th>S. No</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>Grade</th>
                    <th>Title</th>
                    <th>Language</th>
                    <th>Total Sent</th>
                    <th>Total Received</th>
                    <th>Total Damages</th>
                    <th>Total Losts</th>
                    <th>Total Distributed</th>
                    <th>Current Balance</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="(record, index) in provinceDistrictsTabularRecords"
                    v-bind:key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ record.Province }}</td>
                    <td>{{ record.District }}</td>
                    <td>{{ record.Grade }}</td>
                    <td>{{ record.Title }}</td>
                    <td>{{ record.Lang }}</td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalSent == null ? 0 : record.TotalSent
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalReceived == null
                            ? 0
                            : record.TotalReceived
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDamages == null ? 0 : record.TotalDamages
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalLosts == null ? 0 : record.TotalLosts
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDistributed == null
                            ? 0
                            : record.TotalDistributed
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.RemainingBalance == null
                            ? 0
                            : record.RemainingBalance
                        )
                      }}
                    </td>
                  </tr>

                  <tr style="font-weight: bold; background-color: #e2e2e2">
                    <td></td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            provinceDistrictsTabularRecords,
                            "Province"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            provinceDistrictsTabularRecords,
                            "District"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            provinceDistrictsTabularRecords,
                            "Grade"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            provinceDistrictsTabularRecords,
                            "Title"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            provinceDistrictsTabularRecords,
                            "Lang"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceDistrictsTabularRecords, "TotalSent")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(
                            provinceDistrictsTabularRecords,
                            "TotalReceived"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(
                            provinceDistrictsTabularRecords,
                            "TotalDamages"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(provinceDistrictsTabularRecords, "TotalLosts")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(
                            provinceDistrictsTabularRecords,
                            "TotalDistributed"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(
                            provinceDistrictsTabularRecords,
                            "RemainingBalance"
                          )
                        )
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>

              <table
                id="dataTable"
                class="striped"
                v-if="selectedDataType == 'districtschools'"
              >
                <thead>
                  <tr>
                    <th>S. No</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Title</th>
                    <th>Language</th>
                    <th>Total Sent</th>
                    <th>Total Received</th>
                    <th>Total Damages</th>
                    <th>Total Losts</th>
                    <th>Total Distributed</th>
                    <th>Current Balance</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="(record, index) in districtSchoolsTabularRecords"
                    v-bind:key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ record.Province }}</td>
                    <td>{{ record.District }}</td>
                    <td>{{ record.School }}</td>
                    <td>{{ record.Grade }}</td>
                    <td>{{ record.Title }}</td>
                    <td>{{ record.Lang }}</td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalSent == null ? 0 : record.TotalSent
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalReceived == null
                            ? 0
                            : record.TotalReceived
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDamages == null ? 0 : record.TotalDamages
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalLosts == null ? 0 : record.TotalLosts
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.TotalDistributed == null
                            ? 0
                            : record.TotalDistributed
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          record.RemainingBalance == null
                            ? 0
                            : record.RemainingBalance
                        )
                      }}
                    </td>
                  </tr>

                  <tr style="font-weight: bold; background-color: #e2e2e2">
                    <td></td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            districtSchoolsTabularRecords,
                            "Province"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            districtSchoolsTabularRecords,
                            "District"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(
                            districtSchoolsTabularRecords,
                            "School"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(districtSchoolsTabularRecords, "Grade")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(districtSchoolsTabularRecords, "Title")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getUniqueCount(districtSchoolsTabularRecords, "Lang")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(districtSchoolsTabularRecords, "TotalSent")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(districtSchoolsTabularRecords, "TotalReceived")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(districtSchoolsTabularRecords, "TotalDamages")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(districtSchoolsTabularRecords, "TotalLosts")
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(
                            districtSchoolsTabularRecords,
                            "TotalDistributed"
                          )
                        )
                      }}
                    </td>
                    <td>
                      {{
                        formatNumber(
                          getSum(
                            districtSchoolsTabularRecords,
                            "RemainingBalance"
                          )
                        )
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="selectedReportType == 'visual'">
        <div class="col s12">
          <div class="card left-align" style="width: 100%">
            <div class="card-content">
              <span class="card-title">Distributed to Beneficiaries</span>

              <div style="border: 2px solid #e2e2e2">
                <GChart
                  type="PieChart"
                  :data="langsChartData"
                  :options="langsChartOptions"
                />
              </div>

              <div style="margin-top: 20px; border: 2px solid #e2e2e2">
                <GChart
                  type="ColumnChart"
                  :data="districtsChartData"
                  :options="districtsChartOptions"
                />
              </div>

              <div style="margin-top: 20px; border: 2px solid #e2e2e2">
                <GChart
                  type="ColumnChart"
                  :data="gradesChartData"
                  :options="gradesChartOptions"
                />
              </div>

              <div style="margin-top: 20px; border: 2px solid #e2e2e2">
                <GChart
                  type="ColumnChart"
                  :data="titlesChartData"
                  :options="titlesChartOptions"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Download Datasets Modal Structure -->
    <div id="modalDownloadDatasets" class="modal">
      <div class="modal-content">
        <h4>Download Datasets</h4>

        <p>
          Please remember that the complete dataset of the selected type for the
          selected province will be downloaded. Date range and other filters
          will not be applied.
        </p>

        <p>
          The downloaded CSV dataset should be imported to Microsoft Excel using
          its From Text/CSV import feature.
        </p>

        <div class="form-field">
          <label for="datasetTypeDDL">Dataset Type</label>
          <select
            id="datasetTypeDDL"
            name="datasetTypeDDL"
            v-model="selectedDatasetType"
          >
            <option value disabled selected>Select Dataset Type</option>
            <option value="distributed">
              Distributed to Beneficiaries Dataset
            </option>
            <option value="sent">Sent by Vendors to Schools Dataset</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a
          class="modal-close waves-effect light-blue darken-4 btn-small"
          @click="downloadDataSet"
          >Download</a
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Reports",

  created() {
    this.getProvinces();
    this.getProjects();

    M.AutoInit();
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  watch: {
    // get districts of the selected province on province change
    selectedProvinceId: function () {
      this.getDistricts();
    },
  },

  components: {
    LocalDatePicker: VuePersianDatetimePicker,
  },

  data() {
    return {
      showForTypes: [
        "province",
        "provincedistricts",
        "district",
        "districtschools",
      ],

      allProvincesTabularRecords: [],
      provinceTabularRecords: [],
      provinceDistrictsTabularRecords: [],
      districtSchoolsTabularRecords: [],

      selectedReportType: "tabular",
      selectedDataType: "",

      selectedDatasetType: "",

      selectedProvinceId: 0,
      selectedDistrictId: 0,
      selectedProjectId: 0,

      startDate: new Date().toISOString().slice(0, 10),
      endDate: new Date().toISOString().slice(0, 10),

      allProvinces: [],
      selectedProvinceDistricts: [],
      allProjects: [],

      colors: [
        "#3366CC",
        "#FF9900",
        "#DC3912",
        "#cb2ad3",
        "#8fc415",
        "#4DD0E1",
        "#F57C00",
        "#64B5F6",
        "#2e6996",
        "#7678bf",
      ],

      provinceStats: {
        TotalDamaged: 0,
        TotalDistributed: 0,
        TotalLost: 0,
        TotalRceived: 0,
        TotalSent: 0,
      },

      districtsChartData: [
        ["District", "Total", { role: "style" }, { role: "annotation" }],
        [null, 0, "#8fc415", 0],
      ],
      districtsChartOptions: {
        legend: "none",
        title: "BOOKS DISTRIBUTED TO STUDENTS AND TEACHERS BY DISTRICT",
      },

      langsChartData: [
        ["Language", "Total", { role: "annotation" }],
        [null, 0, 0],
      ],
      langsChartOptions: {
        title: "BOOKS DISTRIBUTED TO STUDENTS AND TEACHERS BY LANGUAGE",
        sliceVisibilityThreshold: 0.0001,
        pieSliceText: "value-and-percentage",
      },

      gradesChartData: [
        ["Grade", "Total", { role: "style" }, { role: "annotation" }],
        [null, 0, "#8fc415", 0],
      ],
      gradesChartOptions: {
        legend: "none",
        title: "BOOKS DISTRIBUTED TO STUDENTS AND TEACHERS BY GRADE",
      },

      titlesChartData: [
        ["Title", "Total", { role: "style" }, { role: "annotation" }],
        [null, 0, "#8fc415", 0],
      ],
      titlesChartOptions: {
        legend: "none",
        title: "BOOKS DISTRIBUTED TO STUDENTS AND TEACHERS BY TITLE",
      },
    };
  },

  methods: {
    printTable(table) {
      var divToPrint = document.getElementById(table);
      divToPrint.border = 1;
      divToPrint.cellPadding = 5;
      divToPrint.style.borderCollapse = "collapse";
      divToPrint.align = "center";

      var provinceEL = document.getElementById("levelProvinceDDL");

      var provinceName = "All Provinces";

      if (provinceEL) {
        provinceName = provinceEL.options[provinceEL.selectedIndex].text;
      }

      var finalToPrint =
        "<div style = 'text-align: center;'><h2>" +
        provinceName +
        "</h2><h4>" +
        this.startDate +
        " to " +
        this.endDate +
        "</h4>" +
        divToPrint.outerHTML +
        "</div>";

      var newWin = window.open("");
      newWin.document.write(finalToPrint);
      newWin.document.close();

      newWin.print();
    },

    tableToExcel(table, name, filename) {
      var uri = "data:application/vnd.ms-excel;base64,";
      var template =
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
      var base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)));
      };

      var format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) {
          return c[p];
        });
      };

      if (!table.nodeType) table = document.getElementById(table);

      var provinceEL = document.getElementById("levelProvinceDDL");

      var provinceName = "All Provinces";

      if (provinceEL) {
        provinceName = provinceEL.options[provinceEL.selectedIndex].text;
      }

      var finalTable =
        "<tr><td style='font-weight: bold; background-color: #d3d3d3;' align='center' colspan='" +
        table.rows[0].cells.length +
        "'>" +
        provinceName +
        "</td></tr>" +
        "<tr><td style='font-weight: bold; background-color: #e2e2e2;' align='center' colspan='" +
        table.rows[0].cells.length +
        "'>" +
        this.startDate +
        " to " +
        this.endDate +
        "</td></tr>" +
        table.innerHTML;

      var ctx = { worksheet: name || "Worksheet", table: finalTable };

      document.getElementById("dlink").href =
        uri + base64(format(template, ctx));
      document.getElementById("dlink").download = filename;
      document.getElementById("dlink").click();
    },

    showFor(type) {
      console.log(type);
      var result = this.showForTypes.includes(type);

      return result;
    },

    downloadDataSet() {
      if (this.selectedDatasetType == "sent") {
        this.downloadSentReceivedDataset();
      } else if (this.selectedDatasetType == "distributed") {
        this.downloadDistributionDataset();
      } else {
        console.log("Please select a valid dataset type.");
      }
    },

    downloadDistributionDataset() {
      var instance = this;

      this.$http
        .get("/reports/students/" + this.selectedProvinceId + "/excel", {
          responseType: "blob",
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response);
          instance.forceFileDownload(
            response,
            "DistributionToBeneficiaries.csv"
          );
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    downloadSentReceivedDataset() {
      var instance = this;

      this.$http
        .get("/reports/sent/" + this.selectedProvinceId + "/excel", {
          responseType: "blob",
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response);
          instance.forceFileDownload(response, "VendorsToSchools.csv");
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    forceFileDownload: function (response, filename) {
      const url = window.URL.createObjectURL(response.data);
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", filename);
      link.click();
    },

    getReports() {
      console.log(this.startDate);
      console.log(this.endDate);
      console.log(this.selectedProvinceId);
      console.log(this.selectedProjectId);
      console.log(this.selectedDistrictId);

      // this.getProvinceStats();

      if (this.selectedReportType == "visual") {
        this.getProvinceVisualReports();
      } else if (this.selectedReportType == "tabular") {
        this.getProvinceTabularReports();
      }
    },

    getProvinceVisualReports() {
      this.getDistributedToStudentsByLanguage();

      this.getDistributedToStudentsByDistricts();
      this.getDistributedToStudentsByGrades();
      this.getDistributedToStudentsByTitles();
    },

    getProvinceTabularReports() {
      if (this.selectedDataType == "province") {
        this.getProvinceTabularRecords();
      } else if (this.selectedDataType == "provincedistricts") {
        this.getProvinceDistrictsTabularRecords();
      } else if (this.selectedDataType == "districtschools") {
        this.getDistrictSchoolsTabularRecords();
      } else if (this.selectedDataType == "district") {
        this.getDistrictTabularRecords();
      } else if (this.selectedDataType == "allprovinces") {
        this.getAllProvincesTabularRecords();
      }
    },

    getDistrictSchoolsTabularRecords: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/province/district/schools/tabular",
          {
            provinceId: this.selectedProvinceId,
            districtId: this.selectedDistrictId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.districtSchoolsTabularRecords = response.data;
          console.log(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistrictTabularRecords: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/province/district/tabular",
          {
            provinceId: this.selectedProvinceId,
            districtId: this.selectedDistrictId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.provinceDistrictsTabularRecords = response.data;
          console.log(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getProvinceDistrictsTabularRecords: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/province/districts/tabular",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.provinceDistrictsTabularRecords = response.data;
          console.log(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getProvinceTabularRecords: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/province/tabular",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.provinceTabularRecords = response.data;
          console.log(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getAllProvincesTabularRecords: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/provinces/tabular",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.allProvincesTabularRecords = response.data;
          console.log(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getProvinceStats: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/province/stats",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.provinceStats = {
            TotalDamaged: 0,
            TotalDistributed: 0,
            TotalLost: 0,
            TotalRceived: 0,
            TotalSent: 0,
          };

          if (response.data[0]) {
            console.log(response.data[0]);
            instance.provinceStats = response.data[0];
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistributedToStudentsByTitles: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/students/province/titles",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          var data = response.data;

          instance.titlesChartData = [
            ["Title", "Total", { role: "style" }, { role: "annotation" }],
            [null, 0, "#8fc415", 0],
          ];

          if (data.length > 0) {
            instance.titlesChartData = [
              ["Title", "Total", { role: "style" }, { role: "annotation" }],
            ];
          }

          data.forEach(function (item) {
            var tempItem = [
              item.Title,
              parseInt(item.TotalDistributed),
              instance.colors[
                Math.floor(Math.random() * instance.colors.length)
              ],
              parseInt(item.TotalDistributed),
            ];

            instance.titlesChartData.push(tempItem);
          });
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistributedToStudentsByGrades: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/students/province/grades",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          var data = response.data;

          instance.gradesChartData = [
            ["Grade", "Total", { role: "style" }, { role: "annotation" }],
            [null, 0, "#8fc415", 0],
          ];

          if (data.length > 0) {
            instance.gradesChartData = [
              ["Grade", "Total", { role: "style" }, { role: "annotation" }],
            ];
          }

          data.forEach(function (item) {
            var tempItem = [
              item.Grade,
              parseInt(item.TotalDistributed),
              instance.colors[
                Math.floor(Math.random() * instance.colors.length)
              ],
              parseInt(item.TotalDistributed),
            ];

            instance.gradesChartData.push(tempItem);
          });
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistributedToStudentsByLanguage: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/students/province/languages",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          var data = response.data;

          instance.langsChartData = [
            ["Language", "Total", { role: "annotation" }],
            [null, 0, 0],
          ];

          data.forEach(function (item) {
            var tempItem = [
              item.Lang,
              parseInt(item.TotalDistributed),
              parseInt(item.TotalDistributed),
            ];

            instance.langsChartData.push(tempItem);
          });
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistributedToStudentsByDistricts: function () {
      var instance = this;

      this.$http
        .post(
          "/reports/students/province/districts",
          {
            provinceId: this.selectedProvinceId,
            projectId: this.selectedProjectId,
            startDate: this.startDate,
            endDate: this.endDate,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          var data = response.data;

          instance.districtsChartData = [
            ["District", "Total", { role: "style" }, { role: "annotation" }],
            [null, 0, "#8fc415", 0],
          ];

          if (data.length > 0) {
            instance.districtsChartData = [
              ["District", "Total", { role: "style" }, { role: "annotation" }],
            ];
          }

          data.forEach(function (item) {
            var tempItem = [
              item.District,
              parseInt(item.TotalDistributed),
              instance.colors[
                Math.floor(Math.random() * instance.colors.length)
              ],
              parseInt(item.TotalDistributed),
            ];

            instance.districtsChartData.push(tempItem);
          });
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getProvinces: function () {
      var instance = this;

      this.$http
        .get("/meta/provinces", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          instance.allProvinces = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getProjects: function () {
      var instance = this;

      this.$http
        .get("/meta/projects", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          instance.allProjects = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistricts: function () {
      var instance = this;

      this.$http
        .get("/meta/provinces/" + this.selectedProvinceId + "/districts", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          instance.selectedProvinceDistricts = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getUniqueCount: function (items, prop) {
      var unique = [];

      if (items == null) {
        return unique.length;
      } else {
        items.forEach(function (item) {
          if (!unique.includes(item[prop])) {
            unique.push(item[prop]);
          }
        });

        return unique.length;
      }
    },

    getSum: function (items, prop) {
      if (items == null) {
        return 0;
      }

      var result = items.reduce(function (a, b) {
        return b[prop] == null ? a : a + parseInt(b[prop]);
      }, 0);

      return result;
    },

    formatNumber: function (num) {
      if (num != null) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
      }
    },
  },
};
</script>

<style>
</style>