<template>
  <div>
    <div class="container" id="adminSyncSchoolsPage" style="width: 100%">
      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%">
            <div class="card-content">
              <span class="card-title">Sync Action</span>
              <p>
                Please select what needs to be synced with EMIS from the list
                below.
              </p>

              <br />
              <div class="row">
                <div class="input-field col s12">
                  <select v-model="action">
                    <option value disabled selected>Select Action</option>
                    <option value="provinces">Sync Provinces</option>
                    <option value="districts">Sync Districts</option>
                    <option value="schools">Sync Schools</option>
                  </select>
                  <label>Sync Action</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <section name="schoolsSection" v-show="action == 'schools'">
        <div class="row">
          <div class="col s12">
            <div class="card left-align" style="width: 100%">
              <div class="card-content">
                <span class="card-title">Compare School Lists</span>
                <p>
                  Please select a province and a district and the system will
                  display the list of schools for the selected district in OTIMS
                  and EMIS. If you want to compare and update the list of
                  schools in OTIMS based on the list of EMIS, click the Compare
                  Lists button.
                </p>

                <br />

                <div class="input-field col s12">
                  <select v-model="general.province">
                    <option value disabled selected>Select Province</option>
                    <option
                      v-for="(province, index) in allProvinces"
                      v-bind:key="index"
                      :value="province.id"
                    >
                      {{ province.en_name }}
                    </option>
                  </select>
                  <label>Province</label>
                </div>

                <div class="input-field col s12">
                  <select v-model="general.district">
                    <option value disabled selected>Select District</option>
                    <option
                      v-for="(district, index) in selectedProvinceDistricts"
                      v-bind:key="index"
                      :value="district.id"
                    >
                      {{ district.en_name }}
                    </option>
                  </select>
                  <label>District</label>
                </div>
                <br />

                <div class="row">
                  <div class="form-field col s6">
                    <label
                      >Schools in OTIMS ({{
                        general.otimsSchools.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="school in general.otimsSchools"
                        v-bind:key="school.id"
                        :value="school.id"
                      >
                        {{ school.description + " - " + school.title }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s6">
                    <label
                      >Schools in EMIS ({{ general.emisSchools.length }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="school in general.emisSchools"
                        v-bind:key="school.id"
                        :value="school.id"
                      >
                        {{ school.SchoolCode + " - " }}
                        {{
                          school.NameDr.length > 0
                            ? school.NameDr
                            : school.NamePs.length > 0
                            ? school.NamePs
                            : school.NameEng
                        }}
                      </option>
                    </select>
                  </div>
                </div>

                <button
                  @click="compareSchoolLists"
                  :disabled="general.emisSchools.length < 1"
                  class="waves-effect light-blue darken-4 btn btn-small"
                >
                  Compare Lists
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col s12">
            <div class="card left-align" style="width: 100%">
              <div class="card-content">
                <span class="card-title">Sync School Lists</span>
                <p>
                  The list of schools that are missing in OTIMS, schools that
                  can be updated, and schools that should be deleted from OTIMS
                  are displayed below. Please click the Update Schools in OTIMS
                  button to sync the schools with EMIS.
                </p>
                <br />

                <div class="row">
                  <div class="form-field col s4">
                    <label style="color: red"
                      >Schools Missing in OTIMS ({{
                        general.missingSchools.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="school in general.missingSchools"
                        v-bind:key="school.id"
                        :value="school.id"
                      >
                        {{ school.SchoolCode + " - " }}
                        {{
                          school.NameDr.length > 0
                            ? school.NameDr
                            : school.NamePs.length > 0
                            ? school.NamePs
                            : school.NameEng
                        }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s4">
                    <label style="color: red"
                      >Schools Updateable in OTIMS ({{
                        general.updateableSchools.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="school in general.updateableSchools"
                        v-bind:key="school.id"
                        :value="school.id"
                      >
                        {{ school.SchoolCode + " - " }}
                        {{
                          school.NameDr.length > 0
                            ? school.NameDr
                            : school.NamePs.length > 0
                            ? school.NamePs
                            : school.NameEng
                        }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s4">
                    <label style="color: red"
                      >Schools Deleteable in OTIMS ({{
                        general.deleteableSchools.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="school in general.deleteableSchools"
                        v-bind:key="school.id"
                        :value="school.id"
                      >
                        {{ school.description + " - " }}
                        {{ school.title }}
                      </option>
                    </select>
                  </div>
                </div>

                <button
                  :disabled="
                    general.missingSchools.length < 1 &&
                    general.updateableSchools.length < 1 &&
                    general.deleteableSchools.length < 1
                  "
                  @click="syncSchools"
                  class="waves-effect light-blue darken-4 btn btn-small"
                >
                  Update Schools in OTIMS
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section name="provincesSection" v-show="action == 'provinces'">
        <div class="row">
          <div class="col s12">
            <div class="card left-align" style="width: 100%">
              <div class="card-content">
                <span class="card-title">Sync Provinces</span>
                <p>
                  The list of provinces that are missing in OTIMS, provinces
                  that can be updated, and provinces that should be deleted from
                  OTIMS are displayed below. Please click the Update Provinces
                  in OTIMS button to sync the provinces with EMIS.
                </p>
                <br />

                <div class="row">
                  <div class="form-field col s4">
                    <label style="color: red"
                      >Provinces Missing in OTIMS ({{
                        generalProvinces.missingProvinces.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="province in generalProvinces.missingProvinces"
                        v-bind:key="province.id"
                        :value="province.id"
                      >
                        {{ province.NameEng }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s4">
                    <label style="color: red"
                      >Provinces Updateable in OTIMS ({{
                        generalProvinces.updateableProvinces.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="province in generalProvinces.updateableProvinces"
                        v-bind:key="province.id"
                        :value="province.id"
                      >
                        {{ province.NameEng }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s4">
                    <label style="color: red"
                      >Provinces Deleteable in OTIMS ({{
                        generalProvinces.deleteableProvinces.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="province in generalProvinces.deleteableProvinces"
                        v-bind:key="province.id"
                        :value="province.id"
                      >
                        {{ province.en_name }}
                      </option>
                    </select>
                  </div>
                </div>

                <button
                  :disabled="
                    generalProvinces.missingProvinces.length < 1 &&
                    generalProvinces.updateableProvinces.length < 1 &&
                    generalProvinces.deleteableProvinces.length < 1
                  "
                  @click="syncProvinces"
                  class="waves-effect light-blue darken-4 btn btn-small"
                >
                  Update Provinces in OTIMS
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section name="districtsSection" v-show="action == 'districts'">
        <div class="row">
          <div class="col s12">
            <div class="card left-align" style="width: 100%">
              <div class="card-content">
                <span class="card-title">Compare Districts</span>
                <p>
                  Please select a province and the system will display the list
                  of districts for the selected province in OTIMS and EMIS. If
                  you want to compare and update the list of districts in OTIMS
                  based on the list of EMIS, click the Compare Lists button.
                </p>

                <br />

                <div class="input-field col s12">
                  <select v-model="generalDistricts.province">
                    <option value disabled selected>Select Province</option>
                    <option
                      v-for="(province, index) in allProvinces"
                      v-bind:key="index"
                      :value="province.id"
                    >
                      {{ province.en_name }}
                    </option>
                  </select>
                  <label>Province</label>
                </div>
                <br />

                <div class="row">
                  <div class="form-field col s6">
                    <label
                      >Districts in OTIMS ({{
                        generalDistricts.otimsDistricts.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="district in generalDistricts.otimsDistricts"
                        v-bind:key="district.id"
                        :value="district.id"
                      >
                        {{ district.en_name }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s6">
                    <label
                      >Districts in EMIS ({{
                        generalDistricts.emisDistricts.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="district in generalDistricts.emisDistricts"
                        v-bind:key="district.DistrictId"
                        :value="district.DistrictId"
                      >
                        {{ district.NameEng }}
                      </option>
                    </select>
                  </div>
                </div>

                <button
                  @click="compareDistricts"
                  :disabled="generalDistricts.emisDistricts.length < 1"
                  class="waves-effect light-blue darken-4 btn btn-small"
                >
                  Compare Lists
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col s12">
            <div class="card left-align" style="width: 100%">
              <div class="card-content">
                <span class="card-title">Sync Districts</span>
                <p>
                  The list of districts that are missing in OTIMS, districts
                  that can be updated, and districts that should be deleted from
                  OTIMS are displayed below. Please click the Update Districts
                  in OTIMS button to sync the districts with EMIS.
                </p>
                <br />

                <div class="row">
                  <div class="form-field col s4">
                    <label style="color: red"
                      >Districts Missing in OTIMS ({{
                        generalDistricts.missingDistricts.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="district in generalDistricts.missingDistricts"
                        v-bind:key="district.DistrictId"
                        :value="district.DistrictId"
                      >
                        {{ district.NameEng }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s4">
                    <label style="color: red"
                      >Districts Updateable in OTIMS ({{
                        generalDistricts.updateableDistricts.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="district in generalDistricts.updateableDistricts"
                        v-bind:key="district.DistrictId"
                        :value="district.DistrictId"
                      >
                        {{ district.NameEng }}
                      </option>
                    </select>
                  </div>

                  <div class="form-field col s4">
                    <label style="color: red"
                      >Districts Deleteable in OTIMS ({{
                        generalDistricts.deleteableDistricts.length
                      }})</label
                    >
                    <select
                      class="browser-default"
                      multiple
                      style="min-height: 400px"
                    >
                      <option
                        v-for="district in generalDistricts.deleteableDistricts"
                        v-bind:key="district.id"
                        :value="district.id"
                      >
                        {{ district.en_name }}
                      </option>
                    </select>
                  </div>
                </div>

                <button
                  :disabled="
                    generalDistricts.missingDistricts.length < 1 &&
                    generalDistricts.updateableDistricts.length < 1 &&
                    generalDistricts.deleteableDistricts.length < 1
                  "
                  @click="syncDistricts"
                  class="waves-effect light-blue darken-4 btn btn-small"
                >
                  Update Districts in OTIMS
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminSyncSchools",

  mounted: function () {},

  created() {
    this.getProvinces();
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  watch: {
    action: function () {
      if (this.action == "provinces") {
        this.general.province = 0;
        this.general.district = 0;

        this.getProvinces();
        this.getEmisProvinces();
      } else if (this.action == "school") {
        this.general.province = 0;
        this.general.district = 0;
      }
    },

    "generalDistricts.province": function () {
      var instance = this;

      this.getOtimsDistricts();

      var selectedProvince = this.allProvinces.find(function (province) {
        if (province.id == instance.generalDistricts.province) {
          instance.getEmisDistricts(province.emis_id);
        }
      });
    },

    "general.province": function () {
      this.general.district = 0;

      this.getDistricts();
    },

    "general.district": function (districtId) {
      this.getSchools(districtId);
    },
  },

  data() {
    return {
      action: "",
      general: {
        province: 0,
        district: 0,

        otimsSchools: [],
        emisSchools: [],

        missingSchools: [],
        updateableSchools: [],
        deleteableSchools: [],
      },

      generalDistricts: {
        province: 0,
        otimsDistricts: [],
        emisDistricts: [],

        missingDistricts: [],
        updateableDistricts: [],
        deleteableDistricts: [],
      },

      generalProvinces: {
        missingProvinces: [],
        updateableProvinces: [],
        deleteableProvinces: [],
      },

      emisProvinces: [],
      allProvinces: [],
      selectedProvinceDistricts: [],
    };
  },

  methods: {
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

    getEmisProvinces: function () {
      var instance = this;

      this.$http
        .get(this.$http.defaults.emisURL + "/province/", {})
        .then(function (response) {
          instance.emisProvinces = response.data;
          instance.compareProvinces();
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getEmisDistricts: function (emis_id) {
      var instance = this;

      this.$http
        .get(this.$http.defaults.emisURL + "/province/" + emis_id, {})
        .then(function (response) {
          instance.generalDistricts.emisDistricts = response.data.Districts;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getOtimsDistricts: function () {
      var instance = this;

      this.$http
        .get(
          "/meta/provinces/" + this.generalDistricts.province + "/districts",
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.generalDistricts.otimsDistricts = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getDistricts: function () {
      var instance = this;

      this.$http
        .get("/meta/provinces/" + this.general.province + "/districts", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          instance.selectedProvinceDistricts = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getSchools: function (districtId) {
      this.general.otimsSchools = [];
      this.general.emisSchools = [];

      this.general.missingSchools = [];
      this.general.updateableSchools = [];
      this.general.deleteableSchools = [];

      var districtObj = this.selectedProvinceDistricts.find(function (item) {
        if (item.id == districtId) {
          return item;
        }
      });

      this.getEmisSchools(districtObj.emis_id);
      this.getOtimsSchools();
    },

    getEmisSchools: function (emisDistrictId) {
      var instance = this;

      this.$http
        .get(this.$http.defaults.emisURL + "/district/" + emisDistrictId, {})
        .then(function (response) {
          console.log(response);
          instance.general.emisSchools = response.data;

          instance.$forceUpdate();
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getOtimsSchools: function () {
      var instance = this;

      this.$http
        .get(
          "/admin/nodes/" +
            this.general.province +
            "/" +
            this.general.district +
            "/schools",
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          instance.general.otimsSchools = response.data;
          instance.$forceUpdate();
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    compareSchoolLists: function () {
      this.findMissingSchools();
      this.findUpdateableSchools();
      this.findDeleteableSchools();
    },

    compareProvinces: function () {
      this.findMissingProvinces();
      this.findUpdateableProvinces();
      this.findDeleteableProvinces();
    },

    compareDistricts: function () {
      this.findMissingDistricts();
      this.findUpdateableDistricts();
      this.findDeleteableDistricts();
    },

    findMissingDistricts: function () {
      var emisDistricts = this.generalDistricts.emisDistricts;
      var otimsDistricts = this.generalDistricts.otimsDistricts;

      var missingArray = emisDistricts.filter(function (emisDistrict) {
        var result = otimsDistricts.find(function (otimsDistrict) {
          return otimsDistrict.emis_id == emisDistrict.DistrictId;
        });

        if (result == undefined) {
          return true;
        }
      });

      // console.log(missingArray);

      this.generalDistricts.missingDistricts = missingArray;
    },

    findUpdateableDistricts: function () {
      var emisDistricts = this.generalDistricts.emisDistricts;
      var otimsDistricts = this.generalDistricts.otimsDistricts;

      var updateableArray = emisDistricts.filter(function (emisDistrict) {
        var result = otimsDistricts.find(function (otimsDistrict) {
          return (
            otimsDistrict.emis_id == emisDistrict.DistrictId &&
            (otimsDistrict.en_name != emisDistrict.NameEng ||
              otimsDistrict.dr_name != emisDistrict.NameDr ||
              otimsDistrict.ps_name != emisDistrict.NamePs)
          );
        });

        if (result) {
          return true;
        }
      });

      // console.log(updateableArray);

      this.generalDistricts.updateableDistricts = updateableArray;
    },

    findDeleteableDistricts: function () {
      var emisDistricts = this.generalDistricts.emisDistricts;
      var otimsDistricts = this.generalDistricts.otimsDistricts;

      var deleteableArray = otimsDistricts.filter(function (otimsDistrict) {
        var result = emisDistricts.find(function (emisDistrict) {
          return emisDistrict.DistrictId == otimsDistrict.emis_id;
        });

        if (result == undefined) {
          return true;
        }
      });

      // console.log(deleteableArray);

      this.generalDistricts.deleteableDistricts = deleteableArray;
    },

    findMissingProvinces: function () {
      var emisProvinces = this.emisProvinces;
      var otimsProvinces = this.allProvinces;

      var missingArray = emisProvinces.filter(function (emisProvince) {
        var result = otimsProvinces.find(function (otimsProvince) {
          return otimsProvince.emis_id == emisProvince.ProvinceId;
        });

        if (result == undefined) {
          return true;
        }
      });

      // console.log(missingArray);

      this.generalProvinces.missingProvinces = missingArray;
    },

    findUpdateableProvinces: function () {
      var emisProvinces = this.emisProvinces;
      var otimsProvinces = this.allProvinces;

      var updateableArray = emisProvinces.filter(function (emisProvince) {
        var result = otimsProvinces.find(function (otimsProvince) {
          return (
            otimsProvince.emis_id == emisProvince.ProvinceId &&
            (otimsProvince.en_name != emisProvince.NameEng ||
              otimsProvince.dr_name != emisProvince.NameDr ||
              otimsProvince.ps_name != emisProvince.NamePs)
          );
        });

        if (result) {
          return true;
        }
      });

      // console.log(updateableArray);

      this.generalProvinces.updateableProvinces = updateableArray;
    },

    findDeleteableProvinces: function () {
      var emisProvinces = this.emisProvinces;
      var otimsProvinces = this.allProvinces;

      var deleteableArray = otimsProvinces.filter(function (otimsProvince) {
        var result = emisProvinces.find(function (emisProvince) {
          return emisProvince.ProvinceId == otimsProvince.emis_id;
        });

        if (result == undefined) {
          return true;
        }
      });

      // console.log(deleteableArray);

      this.generalProvinces.deleteableProvinces = deleteableArray;
    },

    findMissingSchools: function () {
      var emisSchools = this.general.emisSchools;
      var otimsSchools = this.general.otimsSchools;

      var missingArray = emisSchools.filter(function (emisSchool) {
        var result = otimsSchools.find(function (otimsSchool) {
          return otimsSchool.description == emisSchool.SchoolCode;
        });

        if (result == undefined) {
          return true;
        }
      });

      // console.log(missingArray);

      this.general.missingSchools = missingArray;
    },

    findUpdateableSchools: function () {
      var emisSchools = this.general.emisSchools;
      var otimsSchools = this.general.otimsSchools;

      var updateableArray = emisSchools.filter(function (emisSchool) {
        var result = otimsSchools.find(function (otimsSchool) {
          return (
            otimsSchool.description == emisSchool.SchoolCode &&
            otimsSchool.title != emisSchool.NameDr
          );
        });

        if (result) {
          return true;
        }
      });

      // console.log(updateableArray);

      this.general.updateableSchools = updateableArray;
    },

    findDeleteableSchools: function () {
      var emisSchools = this.general.emisSchools;
      var otimsSchools = this.general.otimsSchools;

      var deleteableArray = otimsSchools.filter(function (otimsSchool) {
        var result = emisSchools.find(function (emisSchool) {
          return emisSchool.SchoolCode == otimsSchool.description;
        });

        if (result == undefined) {
          return true;
        }
      });

      // console.log(deleteableArray);

      this.general.deleteableSchools = deleteableArray;
    },

    syncDistricts: function () {
      var syncObj = {};
      syncObj.province_id = this.generalDistricts.province;
      syncObj.missingDistricts = this.generalDistricts.missingDistricts;
      syncObj.updateableDistricts = this.generalDistricts.updateableDistricts;
      syncObj.deleteableDistricts = this.generalDistricts.deleteableDistricts;

      console.log(syncObj);

      var instance = this;

      this.$http
        .post(
          "/admin/sync/districts",
          {
            ...syncObj,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          console.log(response.data);
          instance.action = "";
          alert(response.data.message);
        })
        .catch(function (error) {
          console.log(error);
          alert("Some error occured. Please check the console log.");
        });
    },

    syncProvinces: function () {
      var syncObj = {};
      syncObj.missingProvinces = this.generalProvinces.missingProvinces;
      syncObj.updateableProvinces = this.generalProvinces.updateableProvinces;
      syncObj.deleteableProvinces = this.generalProvinces.deleteableProvinces;

      console.log(syncObj);

      var instance = this;

      this.$http
        .post(
          "/admin/sync/provinces",
          {
            ...syncObj,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          console.log(response.data);
          instance.action = "";
          alert(response.data.message);
        })
        .catch(function (error) {
          console.log(error);
          alert("Some error occured. Please check the console log.");
        });
    },

    syncSchools: function () {
      var syncObj = {};

      syncObj.province = this.general.province;
      syncObj.district = this.general.district;

      syncObj.missingSchools = this.general.missingSchools;
      syncObj.updateableSchools = this.general.updateableSchools;
      syncObj.deleteableSchools = this.general.deleteableSchools;

      console.log(syncObj);

      var instance = this;

      this.$http
        .post(
          "/admin/sync/schools",
          {
            ...syncObj,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          console.log(response.data);
          instance.action = "";
          alert(response.data.message);
        })
        .catch(function (error) {
          console.log(error);
          instance.action = "";
          alert("Some error occured. Please check the console log.");
        });
    },
  },
};
</script>


<style scoped>
.collapsible-body {
  background-color: #fff !important;
}

.receivedStatus {
  text-align: center;
  font-size: 12px;
  border-radius: 3px;
  color: #fff;
  background-color: #2bbbad;
  padding: 1px;
  width: 70px;
}

.pendingStatus {
  text-align: center;
  font-size: 12px;
  border-radius: 3px;
  color: #fff;
  background-color: #db400d;
  padding: 1px;
  width: 70px;
}
</style>