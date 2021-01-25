<template>
  <div>
    <div class="container" id="receivePackagePage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div
            v-if="!storeReceivingPackageSet && !localReceivingPackageSet"
            class="card"
            style="width: 100%;"
          >
            <div class="card-content">
              <span class="card-title">{{$t(`text.pending_packages`)}}</span>

              <table class="striped centered">
                <thead>
                  <tr>
                    <th>{{$t(`text.package_id`)}}</th>
                    <th>{{$t(`text.documents`)}}</th>
                    <th>{{$t(`text.title`)}}</th>
                    <th>{{$t(`text.sent_date`)}}</th>
                    <th>{{$t(`text.from`)}}</th>
                    <th>{{$t(`text.to`)}}</th>
                    <th>{{$t(`text.status`)}}</th>
                    <th>{{$t(`text.action`)}}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="pckg in receivedPackages.data" v-bind:key="pckg.id">
                    <td>{{pckg.recieved_id}}</td>
                     <td>
                    <span v-if="pckg.docs > 0" class="material-icons" style="color: green;">check</span>
                    <span v-if="pckg.docs <= 0" class="material-icons" style="color: red;">close</span>
                    </td>
                    <td>{{pckg.title}}</td>
                    <td>{{moment(pckg.send_date, 'YYYY-M-D').format('jYYYY-jM-jD')}}</td>
                    <td>{{pckg.From_title}}</td>
                    <td>{{pckg.To_title}}</td>
                    <td v-if="pckg.recieved == 1">
                      <div class="receivedStatus">{{$t(`text.received`)}}</div>
                    </td>

                    <td v-if="pckg.recieved == 0">
                      <div class="pendingStatus">{{$t(`text.pending`)}}</div>
                    </td>

                    <td>
                      <button
                        @click="loadPackageDetails(pckg)"
                        class="waves-effect light-blue darken-4 btn btn-small"
                      >{{$t(`text.receive`)}}</button>
                    </td>
                  </tr>

                  <tr v-show="!receivedPackages.data || !receivedPackages.data.length > 0">
                    <td>{{$t(`text.no_pending_packages`)}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="storeReceivingPackageSet || localReceivingPackageSet" class="card">
            <div class="card-content">
              <span class="card-title">{{$t(`text.receive_package`)}}</span>

              <div class="row" v-if="errorMessages && errorMessages.length > 0">
                <div class="col s12">
                  <div class="card-panel orange">
                    <div class="white-text">
                      <ul>
                        <li v-for="(error, index) in errorMessages" v-bind:key="index">{{error}}</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <ul
            v-show="storeReceivingPackageSet || localReceivingPackageSet"
            class="collapsible expandable"
          >
            <li class="active">
              <div class="collapsible-header">
                <i class="material-icons" style="color: #01579B;">info</i>
                {{$t(`text.package_information`)}}
              </div>
              <div class="collapsible-body">
                <table class="striped">
                  <tbody>
                    <tr>
                      <td style="font-weight: bold;">{{$t(`text.shipment_title`)}}</td>
                      <td>{{recvdSelectedPackage.package.title}}</td>

                      <td style="font-weight: bold;">{{$t(`text.from`)}}</td>
                      <td>{{recvdSelectedPackage.package.From_title}}</td>
                    </tr>

                    <tr>
                      <td style="font-weight: bold;">{{$t(`text.shipment_description`)}}</td>
                      <td>{{recvdSelectedPackage.package.description}}</td>

                      <td style="font-weight: bold;">{{$t(`text.to`)}}</td>
                      <td>{{recvdSelectedPackage.package.To_title}}</td>
                    </tr>

                    <tr>
                      <td style="font-weight: bold;">{{$t(`text.sent_date`)}}</td>
                      <td>
                        {{recvdSelectedPackage.package.send_date == null ? "" :
                        moment(recvdSelectedPackage.package.send_date, 'YYYY-M-D').format('jYYYY-jM-jD')
                        }} ({{recvdSelectedPackage.package.send_date}})
                      </td>

                      <td style="font-weight: bold;">{{$t(`text.current_status`)}}</td>
                      <td v-if="recvdSelectedPackage.package.recieved == 1">
                        <div class="receivedStatus">{{$t(`text.received`)}}</div>
                      </td>

                      <td v-if="recvdSelectedPackage.package.recieved == 0">
                        <div class="pendingStatus">{{$t(`text.pending`)}}</div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div class="row" style="margin-top: 10px;">
                  <div class="col s12">
                    <table class="striped centered">
                      <thead>
                        <tr>
                          <th>{{$t(`text.grade`)}}</th>
                          <th>{{$t(`text.subject_title`)}}</th>
                          <th>{{$t(`text.language`)}}</th>
                          <th>{{$t(`text.quantity`)}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(grade, index) in recvdSelectedPackage.allDetails.sent_detail"
                          v-bind:key="index"
                        >
                          <td style="border: 1px solid #e2e2e2;">{{index}}</td>
                          <td style="border: 1px solid #e2e2e2;">
                            <tr
                              v-for="(title, index) in grade"
                              style="border: none;"
                              v-bind:key="index"
                            >
                              <td>{{title.title}}</td>
                            </tr>
                          </td>

                          <td style="border: 1px solid #e2e2e2;">
                            <tr
                              v-for="(title, index) in grade"
                              style="border: none;"
                              v-bind:key="index"
                            >
                              <td>{{title.language}}</td>
                            </tr>
                          </td>

                          <td style="border: 1px solid #e2e2e2;">
                            <tr
                              v-for="(title, index) in grade"
                              style="border: none;"
                              v-bind:key="index"
                            >
                              <td>{{title.total}}</td>
                            </tr>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </li>
            <li class="active">
              <div class="collapsible-header">
                <i class="material-icons" style="color: #01579B;">format_list_numbered</i>
                {{$t(`text.titles_and_quantities`)}}
              </div>
              <div class="collapsible-body">
                <button
                  class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                  href="#modalAddTitle2"
                >{{$t(`text.add_title`)}}</button>

                <button
                  class="waves-effect btn-small"
                  @click="loadSentQuantities"
                >{{$t(`text.fill_with_sent_quantities`)}}</button>
                <p class="grey-text lighten-1">{{$t(`text.add_title_help`)}}</p>

                <div style="margin-top: 20px;">
                  <div v-for="(grade, index) in addedGrades" v-bind:key="index">
                    <span style="font-weight: bold;">{{grade.title}}</span>
                    <hr style="border: 2px solid #F8F8F8;" />

                    <table class="striped centered">
                      <thead>
                        <tr>
                          <th>{{$t(`text.subject_title`)}}</th>
                          <th>{{$t(`text.dari`)}}</th>
                          <th>{{$t(`text.pashto`)}}</th>
                          <th>{{$t(`text.other_languages`)}}</th>
                          <th>{{$t(`text.action`)}}</th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr
                          v-for="(title, index) in filterTitlesByGrade(grade.id)"
                          v-bind:key="index"
                        >
                          <td>{{title.title}}</td>
                          <td>
                            <tr>
                              <td>
                                <label>{{$t(`text.fine`)}}</label>
                                <input
                                  type="number"
                                  :placeholder="$t(`text.fine`)"
                                  v-model="generalInfo.lang[title.id+'_1_'+grade.id]"
                                />
                              </td>
                              <td>
                                <label>{{$t(`text.damaged`)}}</label>
                                <input
                                  type="number"
                                  :placeholder="$t(`text.damaged`)"
                                  v-model="generalInfo.lang_dmg[title.id+'_1_'+grade.id]"
                                />
                              </td>
                              <td>
                                <label>{{$t(`text.total`)}}</label>
                                <span>{{getTitleTotal(title.id+'_1_'+grade.id)}}</span>
                              </td>
                            </tr>
                          </td>
                          <td>
                            <tr>
                              <td>
                                <label>{{$t(`text.fine`)}}</label>
                                <input
                                  type="number"
                                  placeholder="Fine"
                                  v-model="generalInfo.lang[title.id+'_2_'+grade.id]"
                                />
                              </td>
                              <td>
                                <label>{{$t(`text.damaged`)}}</label>
                                <input
                                  type="number"
                                  :placeholder="$t(`text.damaged`)"
                                  v-model="generalInfo.lang_dmg[title.id+'_2_'+grade.id]"
                                />
                              </td>
                              <td>
                                <label>{{$t(`text.total`)}}</label>
                                <span>{{getTitleTotal(title.id+'_2_'+grade.id)}}</span>
                              </td>
                            </tr>
                          </td>

                          <td>
                            <select id="languageDropdown" v-model="title.thirdLang">
                              <option value disabled selected>{{$t(`text.select_language`)}}</option>
                              <option
                                v-for="(lang, index) in thirdLanguages"
                                v-bind:key="index"
                                :value="lang.id"
                              >{{lang.title}}</option>
                            </select>

                            <tr>
                              <td>
                                <label>{{$t(`text.fine`)}}</label>
                                <input
                                  type="number"
                                  :placeholder="$t(`text.fine`)"
                                  v-model="generalInfo.lang[title.id+ '_' + title.thirdLang + '_' +grade.id]"
                                />
                              </td>
                              <td>
                                <label>{{$t(`text.damaged`)}}</label>
                                <input
                                  type="number"
                                  :placeholder="$t(`text.damaged`)"
                                  v-model="generalInfo.lang_dmg[title.id + '_' + title.thirdLang + '_' + grade.id]"
                                />
                              </td>
                              <td>
                                <label>{{$t(`text.total`)}}</label>
                                <span>{{getTitleTotal(title.id + '_' + title.thirdLang + '_' + grade.id)}}</span>
                              </td>
                            </tr>
                          </td>

                          <td>
                            <i
                              class="material-icons"
                              style="color: red; font-size: 40px;"
                              v-on:click="removeTitle(title, grade.id)"
                            >close</i>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <br />
                  </div>
                </div>
              </div>
            </li>
            <li class="active">
              <div class="collapsible-header">
                <i class="material-icons" style="color: #01579B;">store</i>
                {{$t(`text.receive_package`)}}
              </div>
              <div class="collapsible-body">
                <form id="receiveForm" name="receiveForm" @submit="receivePackage">
                  <div class="form-field">
                    <label for="receiveDate">{{$t(`text.receiving_date`)}}</label>
                    <date-picker
                      id="receiveDate"
                      name="receiveDate"
                      class="validate"
                      required
                      aria-required="true"
                      :placeholder="$t(`text.select_date`)"
                      v-model="generalInfo.receiveDate"
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
                          prevMonth: 'تېره مياشت/ماه قبل'
                        }
                      },
                      en: {
                        lang: {
                          label: 'Gregorian / میلادی'
                        }
                      }}"
                    ></date-picker>
                  </div>

                  <button
                    class="waves-effect light-blue darken-4 btn-small"
                    form="receiveForm"
                    type="submit"
                  >{{$t(`text.receive_package`)}}</button>
                </form>

                <p class="grey-text lighten-1">{{$t(`text.receive_help`)}}</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Modal Structure -->
    <div id="modalAddTitle2" class="modal">
      <div class="modal-content">
        <h4>{{$t(`text.add_title`)}}</h4>
        <p>{{$t(`text.add_title_dialog_help`)}}</p>

        <div class="form-field">
          <label for="gradeDropdown">{{$t(`text.grade`)}}</label>
          <select id="gradeDropdown" v-model="addTitleDialogInputs.grade">
            <option value disabled selected>{{$t(`text.select_grade`)}}</option>
            <option
              v-for="(grade, index) in allGrades"
              v-bind:key="index"
              :value="grade.grade.id"
            >{{grade.grade.title}}</option>
          </select>
        </div>

        <div class="form-field">
          <label for="titleDropdown">{{$t(`text.subject_title`)}}</label>
          <select id="titleDropdown" v-model="addTitleDialogInputs.title">
            <option value disabled selected>{{$t(`text.select_title`)}}</option>
            <option
              v-for="(title, index) in selectedGradeTitles.titles"
              v-bind:key="index"
              :value="title.id"
            >{{title.title}}</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a
          v-on:click="addNewTitle"
          class="modal-close waves-effect light-blue darken-4 btn-small"
        >{{$t(`text.add_title`)}}</a>
      </div>
    </div>
  </div>
</template>
          

<script>
export default {
  name: "ReceivePackage",
  mounted() {
    // Initialize Materialize Collapsible Expandible
    var elem = document.querySelector(".collapsible.expandable");
    var instance = M.Collapsible.init(elem, {
      accordion: false
    });

    if (!this.$store.getters.receivingPackageObj) {
      this.getReceivedPackages();
    }
  },

  beforeDestroy() {
    this.$store.commit("setReceivingPackage", null);
    console.log("ReceivingPackage set to null in state store.");
  },

  created() {
    this.getGrades();

    if (this.$store.getters.thirdLanguagesObj) {
      this.thirdLanguages = this.$store.getters.thirdLanguagesObj;
    } else {
      this.getThirdLanguages();
    }

    if (this.$store.getters.receivingPackageObj) {
      var temp = this.$store.getters.receivingPackageObj;
      this.recvdSelectedPackage = temp;
    } else {
      console.log("Select a package to receive.");
    }
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  data() {
    return {
      errorMessages: [],
      localReceivingPackageSet: false,
      receivedPackages: {},

      recvdSelectedPackage: {
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      },

      sentLang: {},

      generalInfo: {
        lang: {},
        lang_dmg: {},
        receiveDate: "",
        received_grades: {
          0: null,
          1: false,
          2: false,
          3: false,
          4: false,
          5: false,
          6: false,
          7: false,
          8: false,
          9: false,
          10: false,
          11: false,
          12: false
        }
      },

      allGrades: [],
      thirdLanguages: [],

      selectedGradeTitles: [],

      addedGrades: [],
      addedTitles: [],
      addTitleDialogInputs: {
        grade: "",
        title: ""
      }
    };
  },

  components: {
    DatePicker: VuePersianDatetimePicker
  },

  watch: {
    // load titles of the selected grade based on grade change
    "addTitleDialogInputs.grade": function(selectedGrade, previousGrade) {
      this.loadGradeTitles(selectedGrade);
    },

    // update added grades based on added titles change
    addedTitles: function(newAddedTitles) {
      this.updateAddedGrades();
    },

    "recvdSelectedPackage.package": function() {
      // console.log(this.isObjectEmpty(this.recvdSelectedPackage.package));

      if (this.isObjectEmpty(this.recvdSelectedPackage.package)) {
        this.localReceivingPackageSet = false;
      } else {
        this.localReceivingPackageSet = true;
      }

      console.log(this.localReceivingPackageSet);
    }
  },

  computed: {
    storeReceivingPackageSet: function() {
      if (this.$store.getters.receivingPackageObj) {
        return true;
      }

      return false;
    }
  },

  methods: {
    getThirdLanguages: function() {
      var instance = this;

      this.$http
        .get("/meta/languages/third", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.thirdLanguages = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    loadSentQuantities: function() {
      // console.log(this.sentLang);
      var tempSentLang = this.sentLang;

      // empty the entered values in fine
      for (var key in this.generalInfo.lang) {
        this.$set(this.generalInfo.lang, key, 0);
      }

      // empty the entered values in damages
      for (var key in this.generalInfo.lang_dmg) {
        this.$set(this.generalInfo.lang_dmg, key, 0);
      }

      // load sent quantities
      for (var key in tempSentLang) {
        this.$set(this.generalInfo.lang, key, tempSentLang[key]);
      }
    },
    loadPackageDetails: function(pckg) {
      this.recvdSelectedPackage.package = pckg;

      this.getRecvdPackageAllDetails(pckg.recieved_id);
    },

    getRecvdPackageAllDetails: function(id) {
      var instance = this;

      this.$http
        .get("/shipments/received/" + id + "/all", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.recvdSelectedPackage.allDetails = response.data;
          console.log(instance.recvdSelectedPackage);

          instance.generateReceivingForm();
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getReceivedPackages: function() {
      var instance = this;

      this.$http
        .get("/shipments/received/pending", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.receivedPackages = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    receivePackage: function(e) {
      e.preventDefault();

      this.errorMessages = [];
      var instance = this;

      var receivePackageObj = {};

      receivePackageObj.recieve_date = this.generalInfo.receiveDate;
      receivePackageObj.recieve_id = this.recvdSelectedPackage.allDetails.sent_shipment.id;
      // receivePackageObj.lang = this.generalInfo.lang;
      // receivePackageObj.lang_dmg = this.generalInfo.lang_dmg;
      receivePackageObj.received_grades = this.generalInfo.received_grades;

      var cleanedLang = this.generalInfo.lang;
      Object.keys(cleanedLang).forEach(function(key) {
        if (
          cleanedLang[key] == "" ||
          cleanedLang[key] == null ||
          cleanedLang[key] == undefined ||
          cleanedLang[key] == 0
        ) {
          console.log(key);
          delete cleanedLang[key];
        }
      });

      var cleanedLangDmgs = this.generalInfo.lang_dmg;
      Object.keys(cleanedLangDmgs).forEach(function(key) {
        if (
          cleanedLangDmgs[key] == "" ||
          cleanedLangDmgs[key] == null ||
          cleanedLangDmgs[key] == undefined ||
          cleanedLangDmgs[key] == 0
        ) {
          console.log(key);
          delete cleanedLangDmgs[key];
        }
      });

      receivePackageObj.lang = cleanedLang;
      receivePackageObj.lang_dmg = cleanedLangDmgs;

      this.addedGrades.forEach(function(grade) {
        receivePackageObj.received_grades[grade.id] = true;
      });

      console.log(receivePackageObj);

      this.$http
        .post(
          "/shipments/receive",
          {
            ...receivePackageObj
          },
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
          instance.$router.push("/");
        })
        .catch(function(error) {
          console.log(error.response);

          if (error.response.data.messages) {
            instance.errorMessages = error.response.data.messages;
          } else {
            instance.errorMessages = [this.$t(`text.receive_error`)];
          }

          alert(this.$t(`text.receive_error`));

          instance.scrollToTop();
        });
    },

    generateReceivingForm: function() {
      console.log("Generating receiving form");
      var gradesTitlesObj = this.recvdSelectedPackage.allDetails.sent_detail;
      var instance = this;

      Object.keys(gradesTitlesObj).forEach(function(key) {
        var gradeTitle = key;
        var titles = gradesTitlesObj[key];

        instance.allGrades.find(grade => {
          if (grade.grade.title === gradeTitle) {
            instance.addedGrades.push(grade.grade);

            grade.titles.find(title => {
              titles.forEach(function(addedTitle) {
                if (addedTitle.title === title.title) {
                  let foundTitle = title;

                  let isTitleAdded = instance.addedTitles.find(title => {
                    if (title.id === foundTitle.id) {
                      return true;
                    }
                    return false;
                  });

                  if (!isTitleAdded) {
                    instance.addedTitles.push(foundTitle);
                  }

                  // store values for later filling the form if fill button clicked
                  if (addedTitle.language === "Dari") {
                    instance.sentLang[
                      foundTitle.id + "_1_" + foundTitle.grade_id
                    ] = addedTitle.total.toString();
                  } else if (addedTitle.language === "Pashto") {
                    instance.sentLang[
                      foundTitle.id + "_2_" + foundTitle.grade_id
                    ] = addedTitle.total.toString();
                  } else {
                    let foundLang;
                    instance.thirdLanguages.find(function(lang) {
                      if (lang.title == addedTitle.language) {
                        foundLang = lang;
                      }
                    });

                    instance.sentLang[
                      foundTitle.id +
                        "_" +
                        foundLang.id +
                        "_" +
                        foundTitle.grade_id
                    ] = addedTitle.total.toString();
                  }
                }
              });
            });
          }
        });
      });
    },
    getTitleTotal: function(title) {
      // console.log(title);
      var parsedFine = parseInt(this.generalInfo.lang[title]);
      var parsedDamaged = parseInt(this.generalInfo.lang_dmg[title]);

      if (isNaN(parsedFine)) {
        parsedFine = 0;
      }
      if (isNaN(parsedDamaged)) {
        parsedDamaged = 0;
      }

      return parsedFine + parsedDamaged;
    },
    updateAddedGrades: function() {
      var instance = this;

      this.addedGrades = [];

      if (this.addedTitles.length > 0) {
        this.addedTitles.find(title => {
          var gradeId = title.grade_id;

          var foundGrade = this.allGrades.filter(grade => {
            if (grade.grade.id === gradeId) {
              return grade.grade;
            }
          });

          foundGrade = foundGrade[0].grade;

          var isAdded = instance.addedGrades.indexOf(foundGrade);

          if (isAdded === -1) {
            instance.addedGrades.push(foundGrade);
          }
        });

        // sort the grades 1 to 12
        this.addedGrades.sort(function(a, b) {
          return a.id - b.id;
        });
      }
    },

    scrollToTop: function() {
      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    },

    isObjectEmpty: function(obj) {
      for (var key in obj) {
        if (obj.hasOwnProperty(key)) return false;
      }
      return true;
    },

    loadGradeTitles: function(selectedGrade) {
      let gradeTitles = this.allGrades.filter(grade => {
        if (grade.grade.id === selectedGrade) {
          return true;
        }
      });

      this.selectedGradeTitles = gradeTitles[0];
    },

    getGrades: function() {
      var instance = this;

      this.$http
        .get("/meta/grades", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.allGrades = response.data;

          if (
            instance.storeReceivingPackageSet ||
            instance.localReceivingPackageSet
          ) {
            instance.generateReceivingForm();
          }
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    removeTitle: function(titleObj, gradeId) {
      var deleteConfirmed = confirm(this.$t(`text.confirm_remove_title`));

      if (deleteConfirmed) {
        let index = this.addedTitles.indexOf(titleObj);
        console.log(index);
        this.addedTitles.splice(index, 1);

        // delete values
        delete this.generalInfo.lang[titleObj.id + "_1_" + gradeId];
        delete this.generalInfo.lang_dmg[titleObj.id + "_1_" + gradeId];
        delete this.generalInfo.lang[titleObj.id + "_2_" + gradeId];
        delete this.generalInfo.lang_dmg[titleObj.id + "_2_" + gradeId];

        var instance = this;
        this.thirdLanguages.forEach(function(lang) {
          delete instance
            .generalInfo.lang[titleObj.id + "_" + lang.id + "_" + gradeId];
          delete instance
            .generalInfo.lang_dmg[titleObj.id + "_" + lang.id + "_" + gradeId];
        });

        // console.log(this.generalInfo);
      }
    },

    addNewTitle: function() {
      // console.log(this.allGrades);

      let selectedTitle = {
        grade: this.addTitleDialogInputs.grade,
        title: this.addTitleDialogInputs.title
      };

      let foundTitle;
      let foundGrade;

      this.allGrades.find(grade => {
        if (grade.grade.id === selectedTitle.grade) {
          foundGrade = grade.grade;

          grade.titles.find(title => {
            if (title.id === selectedTitle.title) {
              foundTitle = title;
            }
          });
        }
      });

      if (foundTitle != undefined) {
        // add the title to array if not already added
        let isTitleAdded = this.addedTitles.find(title => {
          if (title.id === foundTitle.id) {
            return true;
          }
          return false;
        });

        if (!isTitleAdded) {
          this.addedTitles.push(foundTitle);
        } else {
          alert(
            this.$t("text.title_already_added", [
              foundTitle.title,
              foundGrade.title
            ])
          );
        }
      } else {
        alert(this.$t(`text.select_specific_grade_title`));
      }
    },

    filterTitlesByGrade: function(currentGrade) {
      let filteredByGrade = this.addedTitles.filter(title => {
        if (title.grade_id == currentGrade) {
          return true;
        }
      });

      return filteredByGrade;
    }
  }
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