<template>
  <div>
    <div class="container" id="srBenenficiaryPage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card">
            <div class="card-content">
              <span
                class="card-title"
              >{{transactionType == "send"? $t(`text.deliver_to_students`) : $t(`text.receive_from_students`)}}</span>

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

          <ul class="collapsible expandable">
            <li class="active">
              <div class="collapsible-header">
                <i class="material-icons" style="color: #01579B;">info</i>
                {{$t(`text.package_information`)}}
              </div>
              <div class="collapsible-body">
                <form id="generalForm" @submit="sendPackage">
                  <div class="form-field">
                    <label for="ShipmentTitle">{{$t(`text.shipment_title`)}}</label>
                    <input
                      type="text"
                      id="ShipmentTitle"
                      name="ShipmentTitle"
                      :placeholder="$t(`text.shipment_title`)"
                      class="validate"
                      required
                      aria-required="true"
                      v-model="generalInfo.title"
                    />
                  </div>

                  <br />
                  <div class="form-field">
                    <label for="ShipmentDescription">{{$t(`text.shipment_description`)}}</label>
                    <input
                      type="text"
                      id="ShipmentDescription"
                      name="ShipmentDescription"
                      :placeholder="$t(`text.shipment_description`)"
                      v-model="generalInfo.description"
                    />
                  </div>

                  <br />

                  <div class="form-field">
                    <label for="projectDDL">{{$t(`text.textbooks_project`)}}</label>
                    <select id="projectDDL" name="projectDDL" v-model="generalInfo.project">
                      <option value disabled selected>{{$t(`text.select_project`)}}</option>
                      <option
                        v-for="(project, index) in allProjects"
                        v-bind:key="index"
                        :value="project.id"
                      >{{project.title}}</option>
                    </select>
                  </div>

                  <br />

                  <div class="form-field">
                    <label for="ShipmentTime">{{$t(`text.shipment_date`)}}</label>

                    <date-picker
                      id="ShipmentTime"
                      name="ShipmentTime"
                      class="validate"
                      required
                      aria-required="true"
                      v-model="generalInfo.date"
                      :placeholder="$t(`text.select_date`)"
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
                </form>
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
                  href="#modalAddTitle"
                >{{$t(`text.add_title`)}}</button>

                <button
                  class="waves-effect btn btn-small modal-trigger"
                  href="#modalLoadTemplate"
                >{{$t(`text.load_template`)}}</button>

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
                            <input
                              type="number"
                              :placeholder="$t(`text.quantity`)"
                              v-model="generalInfo.lang[title.id+'_1_'+grade.id]"
                            />
                          </td>
                          <td>
                            <input
                              type="number"
                              :placeholder="$t(`text.quantity`)"
                              v-model="generalInfo.lang[title.id+'_2_'+grade.id]"
                            />
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

                            <input
                              type="number"
                              :placeholder="$t(`text.quantity`)"
                              v-model="generalInfo.lang[title.id + '_' + title.thirdLang + '_' + grade.id]"
                            />
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
                {{transactionType == "send"? $t(`text.send_package`) : $t(`text.receive_package`)}}
              </div>
              <div class="collapsible-body">
                <button
                  class="waves-effect light-blue darken-4 btn-small"
                  form="generalForm"
                  type="submit"
                >{{transactionType == "send"? $t(`text.send_package`) : $t(`text.receive_package`)}}</button>

                <p class="grey-text lighten-1">
                  {{ $t("text.send_receive_help", [
                  transactionType == "send"? $t(`text.send_package`) : $t(`text.receive_package`)
                  ]) }}
                </p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Modal Structure -->
    <div id="modalAddTitle" class="modal">
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

    <div id="modalLoadTemplate" class="modal">
      <div class="modal-content">
        <h4>{{$t(`text.load_template`)}}</h4>
        <p>{{$t(`text.load_template_dialog_help`)}}</p>

        <div class="form-field">
          <label for="templateDropdown">{{$t(`text.template`)}}</label>
          <select id="templateDropdown" v-model="selectedTemplate">
            <option value disabled selected>{{$t(`text.select_template`)}}</option>
            <option value="Pashto">ACR TLMs Pashto</option>
            <option value="Dari">ACR TLMs Dari</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a
          v-on:click="loadTemplate"
          class="modal-close waves-effect light-blue darken-4 btn-small"
        >{{$t(`text.load_template`)}}</a>
      </div>
    </div>

    <div id="modalConfirmSend" class="modal">
      <div class="modal-content">
        <h4>{{$t(`text.confirm_shipment_details`)}}</h4>
        <p>
          {{ $t("text.confirm_send_receive_help", [
          transactionType == "send"? $t(`text.send_package`) : $t(`text.receive_package`)
          ]) }}
        </p>

        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_title`)}}</td>
              <td>{{packageObj.title}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_description`)}}</td>
              <td>{{packageObj.description}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_recipient`)}}</td>
              <td>{{beingSentShipmentRecipientNode}}</td>
            </tr>
          </tbody>
        </table>

        <table class="centered">
          <thead>
            <tr>
              <th>{{$t(`text.grade`)}}</th>
              <th>{{$t(`text.subject_title`)}}</th>
              <th>{{$t(`text.language`)}}</th>
              <th>{{$t(`text.quantity`)}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(grade, index) in beingSentAddedGrades" v-bind:key="index">
              <td style="border: 1px solid #e2e2e2;">{{grade.title}}</td>
              <td style="border: 1px solid #e2e2e2;">
                <tr
                  v-for="(title, index) in filterBeingSentTitlesByGrade(grade.id)"
                  v-bind:key="index"
                  style="border: none;"
                >
                  <td>{{title.title}}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2;">
                <tr
                  v-for="(title, index) in filterBeingSentTitlesByGrade(grade.id)"
                  v-bind:key="index"
                  style="border: none;"
                >
                  <td>{{title.language}}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2;">
                <tr
                  v-for="(title, index) in filterBeingSentTitlesByGrade(grade.id)"
                  v-bind:key="index"
                  style="border: none;"
                >
                  <td>{{title.total}}</td>
                </tr>
              </td>
            </tr>
            <tr>
              <td style="font-weight: bold;">{{$t(`text.grand_total`)}}</td>
              <td></td>
              <td></td>
              <td style="font-weight: bold;">{{beingSentGrandTotal}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <a
          @click="postPackage"
          class="modal-close waves-effect light-blue darken-4 btn-small"
        >{{transactionType == "send"? $t(`text.send_package`) : $t(`text.receive_package`)}}</a>
        <a class="modal-close waves-effect btn-small">{{$t(`text.cancel`)}}</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SendReceiveBeneficiary",
  mounted() {
    // Initialize Materialize Collapsible Expandible
    var elem = document.querySelector(".collapsible.expandable");
    var instance = M.Collapsible.init(elem, {
      accordion: false
    });
  },

  created() {
    if (this.$store.getters.srBeneficiaryObj) {
      var srBeneficiaryObj = this.$store.getters.srBeneficiaryObj;
      this.transactionType = srBeneficiaryObj.type;
      this.generalInfo.node = srBeneficiaryObj.nodeId;
    } else {
      this.$router.push("/student-dist");
    }

    this.getGrades();
    this.getProjects();

    if (this.$store.getters.thirdLanguagesObj) {
      this.thirdLanguages = this.$store.getters.thirdLanguagesObj;
    } else {
      this.getThirdLanguages();
    }
  },

  beforeDestroy() {
    this.$store.commit("setSRBeneficiaryObj", null);
    console.log("srBeneficiaryObj set to null in state store.");
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  data() {
    return {
      packageObj: { lang: {} },
      beingSentAddedGrades: [],
      beingSentAddedTitles: [],

      selectedTemplate: "",

      errorMessages: [],

      allGrades: [],
      allProjects: [],
      thirdLanguages: [],

      transactionType: "",

      generalInfo: {
        title: "",
        description: "",
        node: 0,
        project: 0,
        date: "",
        lang: {},
        sent_grades: {
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

  computed: {
    beingSentGrandTotal: function() {
      var grandTotal = 0;

      for (var key in this.packageObj.lang) {
        grandTotal += parseInt(this.packageObj.lang[key]);
      }
      return grandTotal;
    },

    beingSentShipmentRecipientNode: function() {
      var node = "";

      if (this.transactionType == "receive") {
        node = this.$t(`text.self_selected_school`);
      } else if (this.transactionType == "send") {
        node = this.$t(`text.beneficiaries`);
      }

      return node;
    }
  },

  watch: {
    // load titles of the selected grade based on grade change
    "addTitleDialogInputs.grade": function(selectedGrade, previousGrade) {
      this.loadGradeTitles(selectedGrade);
    },

    // update added grades based on added titles change
    addedTitles: function(newAddedTitles) {
      this.updateAddedGrades();
    }
  },

  methods: {
    loadTemplate: function() {
      let lang = this.selectedTemplate;

      if (lang == "Pashto") {
        this.loadPashtoTemplate();
      } else if (lang == "Dari") {
        this.loadDariTemplate();
      }
    },

    loadPashtoTemplate: function() {
      this.addedTitles = [];
      this.generalInfo.lang = {};

      let instance = this;
      let desiredTitles = [
        3,
        211,
        212,
        213,

        17,
        217,
        218,
        219,

        31,
        223,
        224,
        225
      ];

      this.allGrades.forEach(grade => {
        if (grade.grade.id == 1 || grade.grade.id == 2 || grade.grade.id == 3) {
          desiredTitles.forEach(desiredTitle => {
            grade.titles.find(title => {
              if (title.id === desiredTitle) {
                instance.addedTitles.push(title);
              }
            });
          });
        }
      });

      this.updateAddedGrades();
    },

    loadDariTemplate: function() {
      this.addedTitles = [];
      this.generalInfo.lang = {};

      let instance = this;
      let desiredTitles = [
        10,
        210,
        214,
        215,

        24,
        216,
        220,
        221,

        38,
        222,
        226,
        227
      ];

      this.allGrades.forEach(grade => {
        if (grade.grade.id == 1 || grade.grade.id == 2 || grade.grade.id == 3) {
          desiredTitles.forEach(desiredTitle => {
            grade.titles.find(title => {
              if (title.id === desiredTitle) {
                instance.addedTitles.push(title);
              }
            });
          });
        }
      });

      this.updateAddedGrades();
    },

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

    sendPackage: function(e) {
      e.preventDefault();
      this.errorMessages = [];

      var instance = this;

      if (
        this.generalInfo.node != 0 &&
        Object.keys(this.generalInfo.lang).length > 0
      ) {
        let packageObj = {};

        packageObj.title = this.generalInfo.title;
        packageObj.description = this.generalInfo.description;
        packageObj.node_id = this.generalInfo.node.toString();
        packageObj.project_id = this.generalInfo.project.toString();
        packageObj.send_date = this.generalInfo.date;

        var cleanedLang = this.generalInfo.lang;

        Object.keys(cleanedLang).forEach(function(key) {
          if (
            cleanedLang[key] == "" ||
            cleanedLang[key] == null ||
            cleanedLang[key] == undefined ||
            cleanedLang[key] == 0
          ) {
            delete cleanedLang[key];
          }
        });

        packageObj.lang = cleanedLang;

        if (this.transactionType == "send") {
          packageObj.sent_grades = this.generalInfo.sent_grades;
          this.addedGrades.forEach(function(grade) {
            packageObj.sent_grades[grade.id] = true;
          });
        } else if (this.transactionType == "receive") {
          packageObj.receive_grades = this.generalInfo.sent_grades;
          this.addedGrades.forEach(function(grade) {
            packageObj.receive_grades[grade.id] = true;
          });
        }

        // show confirmation dialog
        console.log(packageObj);

        var modalConfirm = document.querySelector("#modalConfirmSend");
        var instance = M.Modal.getInstance(modalConfirm);
        instance.open();

        this.packageObj = packageObj;
        this.confirmSendPackage(packageObj.lang);
        // show confirmation dialog
      } else {
        this.scrollToTop();

        if (this.generalInfo.node == 0) {
          this.errorMessages.push(instance.$t(`text.select_sr_node`));
        }

        if (Object.keys(this.generalInfo.lang).length < 1) {
          this.errorMessages.push(instance.$t(`text.add_some_titles`));
        }
      }
    },

    postPackage: function() {
      if (this.transactionType == "send") {
        this.postSendPackage();
      } else if (this.transactionType == "receive") {
        this.postReceivePackage();
      }
    },

    postSendPackage: function() {
      var instance = this;
      var packageObj = this.packageObj;

      this.$http
        .post(
          "/beneficiary/send",
          {
            ...packageObj
          },
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
          instance.$router.push("/student-dist");
        })
        .catch(function(error) {
          console.log(error.response);

          if (error.response.data.messages) {
            instance.errorMessages = error.response.data.messages;
          } else {
            instance.errorMessages = [instance.$t(`text.correct_form_error`)];
          }

          alert(instance.$t(`text.correct_form_error`));

          instance.scrollToTop();
        });
    },

    postReceivePackage: function() {
      var instance = this;
      var packageObj = this.packageObj;

      this.$http
        .post(
          "/beneficiary/receive",
          {
            ...packageObj
          },
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
          instance.$router.push("/student-dist");
        })
        .catch(function(error) {
          console.log(error.response);

          if (error.response.data.messages) {
            instance.errorMessages = error.response.data.messages;
          } else {
            instance.errorMessages = [this.$t(`text.correct_form_error`)];
          }

          alert(this.$t(`text.correct_form_error`));

          instance.scrollToTop();
        });
    },

    confirmSendPackage: function(lang) {
      this.beingSentAddedGrades = [];
      this.beingSentAddedTitles = [];

      var instance = this;

      var foundGrades = [];

      for (var key in lang) {
        var title = key.split("_");

        // find the grade object and push to array
        if (!foundGrades.includes(title[2])) {
          foundGrades.push(title[2]);

          this.addedGrades.find(function(grade) {
            if (grade.id == title[2]) {
              instance.beingSentAddedGrades.push(grade);
            }
          });
        }

        // find the title object and push to array
        this.addedTitles.find(function(item) {
          if (item.id == title[0]) {
            var tempItem = {
              ...item
            };

            if (title[1] == 1) {
              tempItem.language = "Dari";
              tempItem.total = lang[key];
            } else if (title[1] == 2) {
              tempItem.language = "Pashto";
              tempItem.total = lang[key];
            } else {
              instance.thirdLanguages.find(function(language) {
                if (language.id == title[1]) {
                  tempItem.language = language.title;
                  tempItem.total = lang[key];
                }
              });
            }

            instance.beingSentAddedTitles.push(tempItem);
          }
        });
      }

      // sort the grades 1 to 12
      this.beingSentAddedGrades.sort(function(a, b) {
        return a.id - b.id;
      });

      console.log(this.beingSentAddedGrades);
      console.log(this.beingSentAddedTitles);
    },

    scrollToTop: function() {
      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
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
          // console.log(response.data);

          instance.allGrades = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getProjects: function() {
      var instance = this;

      this.$http
        .get("/meta/projects", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          // console.log(response.data);

          instance.allProjects = response.data;
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
        delete this.generalInfo.lang[titleObj.id + "_2_" + gradeId];

        var instance = this;
        this.thirdLanguages.forEach(function(lang) {
          delete instance
            .generalInfo.lang[titleObj.id + "_" + lang.id + "_" + gradeId];
        });

        console.log(this.generalInfo.lang);
      }
    },

    addNewTitle: function() {
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
    },

    filterBeingSentTitlesByGrade: function(currentGrade) {
      let filteredByGrade = this.beingSentAddedTitles.filter(title => {
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
</style>