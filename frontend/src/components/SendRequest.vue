<template>
  <div>
    <div class="container" id="sendRequestPage" style="width: 100%">
      <div class="row">
        <div class="col s12">
          <div class="card">
            <div class="card-content">
              <span class="card-title">Request Books</span>

              <button
                v-show="userNode.level_id == 15"
                @click="showEnrollment"
                class="waves-effect light-blue darken-4 btn-small modal-trigger"
                href="#modalEnrollment"
              >
                View School Enrollment
              </button>

              <div class="row" v-if="errorMessages && errorMessages.length > 0">
                <div class="col s12">
                  <div class="card-panel orange">
                    <div class="white-text">
                      <ul>
                        <li
                          v-for="(error, index) in errorMessages"
                          v-bind:key="index"
                        >
                          {{ error }}
                        </li>
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
                <i class="material-icons" style="color: #01579b">info</i>
                General Information
              </div>
              <div class="collapsible-body">
                <form id="generalForm" @submit="sendPackage">
                  <div class="form-field">
                    <label for="RequestTitle">Request Title</label>
                    <input
                      type="text"
                      id="RequestTitle"
                      name="RequestTitle"
                      placeholder="Request Title"
                      class="validate"
                      required
                      aria-required="true"
                      v-model="generalInfo.title"
                    />
                  </div>

                  <br />
                  <div class="form-field">
                    <label for="RequestDescription">Request Description</label>
                    <input
                      type="text"
                      id="RequestDescription"
                      name="RequestDescription"
                      placeholder="Request Description"
                      v-model="generalInfo.description"
                    />
                  </div>

                  <br />

                  <div class="form-field">
                    <label for="RequestBy">Request By</label>
                    <select
                      id="RequestBy"
                      name="RequestBy"
                      v-model="generalInfo.request_by_node"
                    >
                      <option value disabled selected>Select Node</option>
                      <option v-if="userNode.id" :value="userNode.id">
                        {{ userNode.title }}
                      </option>
                    </select>
                  </div>
                  <br />

                  <div class="form-field">
                    <label for="RequestTo">Request To</label>
                    <select
                      id="RequestTo"
                      name="RequestTo"
                      v-model="generalInfo.request_to_node"
                    >
                      <option value disabled selected>Select Node</option>
                      <option
                        v-if="userNode.parent"
                        :value="userNode.parent.id"
                      >
                        {{ userNode.parent.title }}
                      </option>
                    </select>
                  </div>

                  <br />

                  <div class="form-field">
                    <label for="RequestDate">Request Date</label>

                    <local-date-picker
                      id="RequestDate"
                      name="RequestDate"
                      class="validate"
                      required
                      aria-required="true"
                      :placeholder="$t(`text.select_date`)"
                      v-model="generalInfo.requested_at"
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

                  <br />

                  <div class="form-field">
                    <label for="ExpectedDate">Expected Date</label>

                    <local-date-picker
                      id="ExpectedDate"
                      name="ExpectedDate"
                      class="validate"
                      required
                      aria-required="true"
                      :placeholder="$t(`text.select_date`)"
                      v-model="generalInfo.expected_at"
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
                </form>
              </div>
            </li>
            <li class="active">
              <div class="collapsible-header">
                <i class="material-icons" style="color: #01579b"
                  >format_list_numbered</i
                >
                {{ $t(`text.titles_and_quantities`) }}
              </div>
              <div class="collapsible-body">
                <button
                  class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                  href="#modalAddTitle"
                >
                  {{ $t(`text.add_title`) }}
                </button>

                <p class="grey-text lighten-1">
                  {{ $t(`text.add_title_help`) }}
                </p>

                <div style="margin-top: 20px">
                  <div v-for="(grade, index) in addedGrades" v-bind:key="index">
                    <span style="font-weight: bold">{{ grade.title }}</span>
                    <hr style="border: 2px solid #f8f8f8" />

                    <table class="striped centered">
                      <thead>
                        <tr>
                          <th>{{ $t(`text.subject_title`) }}</th>
                          <th>{{ $t(`text.dari`) }}</th>
                          <th>{{ $t(`text.pashto`) }}</th>
                          <th>{{ $t(`text.other_languages`) }}</th>
                          <th>{{ $t(`text.action`) }}</th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr
                          v-for="(title, index) in filterTitlesByGrade(
                            grade.id
                          )"
                          v-bind:key="index"
                        >
                          <td>{{ title.title }}</td>
                          <td>
                            <input
                              type="number"
                              :placeholder="$t(`text.quantity`)"
                              v-model="
                                generalInfo.lang[title.id + '_1_' + grade.id]
                              "
                            />
                          </td>
                          <td>
                            <input
                              type="number"
                              :placeholder="$t(`text.quantity`)"
                              v-model="
                                generalInfo.lang[title.id + '_2_' + grade.id]
                              "
                            />
                          </td>
                          <td>
                            <select
                              id="languageDropdown"
                              v-model="title.thirdLang"
                            >
                              <option value disabled selected>
                                {{ $t(`text.select_language`) }}
                              </option>
                              <option
                                v-for="(lang, index) in thirdLanguages"
                                v-bind:key="index"
                                :value="lang.id"
                              >
                                {{ lang.title }}
                              </option>
                            </select>

                            <input
                              type="number"
                              :placeholder="$t(`text.quantity`)"
                              v-model="
                                generalInfo.lang[
                                  title.id +
                                    '_' +
                                    title.thirdLang +
                                    '_' +
                                    grade.id
                                ]
                              "
                            />
                          </td>

                          <td>
                            <i
                              class="material-icons"
                              style="color: red; font-size: 40px"
                              v-on:click="removeTitle(title, grade.id)"
                              >close</i
                            >
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
                <i class="material-icons" style="color: #01579b">store</i>
                Request Books
              </div>
              <div class="collapsible-body">
                <button
                  class="waves-effect light-blue darken-4 btn-small"
                  form="generalForm"
                  type="submit"
                >
                  Request Books
                </button>

                <p class="grey-text lighten-1">
                  * Please make sure that you have reviewed all the information
                  you have entered before clicking the Request Books button.
                </p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Modal Structure -->
    <div id="modalEnrollment" class="modal">
      <div class="modal-content">
        <h4>Enrollment Details</h4>
        <p>
          The latest available enrollment details for
          {{ userNode.title }} school are shown below.
        </p>

        <table class="striped">
          <thead>
            <tr>
              <th>Grade</th>
              <th>Present</th>
              <th>Permanent Absent</th>
              <th>Permanent Absent 1yr</th>
              <th>Permanent Absent 2yrs</th>
              <th>Permanent Absent 3yrs</th>
              <th>Present Repeaters</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in enrollment" v-bind:key="item.Id">
              <td style="font-weight: bold">{{ item.GradeId }}</td>
              <td>{{ item.NoOfPresent }}</td>
              <td>{{ item.NoOfPermanentAbsent }}</td>
              <td>{{ item.NoOfPermanentAbsentOneYear }}</td>
              <td>{{ item.NoOfPermanentAbsentTwoYears }}</td>
              <td>{{ item.NoOfPermanentAbsentThreeYears }}</td>
              <td>{{ item.NoOfPresentRepeaters }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small"
          >Close</a
        >
      </div>
    </div>

    <!-- Modal Structure -->
    <div id="modalAddTitle" class="modal">
      <div class="modal-content">
        <h4>{{ $t(`text.add_title`) }}</h4>
        <p>{{ $t(`text.add_title_dialog_help`) }}</p>

        <div class="form-field">
          <label for="gradeDropdown">{{ $t(`text.grade`) }}</label>
          <select id="gradeDropdown" v-model="addTitleDialogInputs.grade">
            <option value disabled selected>
              {{ $t(`text.select_grade`) }}
            </option>
            <option
              v-for="(grade, index) in allGrades"
              v-bind:key="index"
              :value="grade.grade.id"
            >
              {{ grade.grade.title }}
            </option>
          </select>
        </div>

        <div class="form-field">
          <label for="titleDropdown">{{ $t(`text.subject_title`) }}</label>
          <select id="titleDropdown" v-model="addTitleDialogInputs.title">
            <option value disabled selected>
              {{ $t(`text.select_title`) }}
            </option>
            <option
              v-for="(title, index) in selectedGradeTitles.titles"
              v-bind:key="index"
              :value="title.id"
            >
              {{ title.title }}
            </option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a
          v-on:click="addNewTitle"
          class="modal-close waves-effect light-blue darken-4 btn-small"
          >{{ $t(`text.add_title`) }}</a
        >
      </div>
    </div>

    <div id="modalConfirmSend" class="modal">
      <div class="modal-content">
        <h4>Confirm Request Details</h4>
        <p>
          Please review the following and make sure they are correct before
          pressing the Request Books button.
        </p>

        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold">Request Title</td>
              <td>{{ sendPackageObj.title }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request Description</td>
              <td>{{ sendPackageObj.description }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request By</td>
              <td v-if="userNode.id">{{ userNode.title }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request To</td>
              <td v-if="userNode.parent">{{ userNode.parent.title }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request Date</td>
              <td v-if="sendPackageObj.requested_at">
                {{
                  moment(sendPackageObj.requested_at, "YYYY/M/D").format(
                    "jYYYY-jM-jD"
                  )
                }}
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold">Expected Date</td>
              <td v-if="sendPackageObj.expected_at">
                {{
                  moment(sendPackageObj.expected_at, "YYYY/M/D").format(
                    "jYYYY-jM-jD"
                  )
                }}
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <table class="centered">
          <thead>
            <tr>
              <th>{{ $t(`text.grade`) }}</th>
              <th>{{ $t(`text.subject_title`) }}</th>
              <th>{{ $t(`text.language`) }}</th>
              <th>{{ $t(`text.quantity`) }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(grade, index) in beingSentAddedGrades"
              v-bind:key="index"
            >
              <td style="border: 1px solid #e2e2e2">{{ grade.title }}</td>
              <td style="border: 1px solid #e2e2e2">
                <tr
                  v-for="(title, index) in filterBeingSentTitlesByGrade(
                    grade.id
                  )"
                  v-bind:key="index"
                  style="border: none"
                >
                  <td>{{ title.title }}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2">
                <tr
                  v-for="(title, index) in filterBeingSentTitlesByGrade(
                    grade.id
                  )"
                  v-bind:key="index"
                  style="border: none"
                >
                  <td>{{ title.language }}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2">
                <tr
                  v-for="(title, index) in filterBeingSentTitlesByGrade(
                    grade.id
                  )"
                  v-bind:key="index"
                  style="border: none"
                >
                  <td>{{ title.total }}</td>
                </tr>
              </td>
            </tr>
            <tr>
              <td style="font-weight: bold">{{ $t(`text.grand_total`) }}</td>
              <td></td>
              <td></td>
              <td style="font-weight: bold">{{ beingSentGrandTotal }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <a
          @click="postPackage"
          class="modal-close waves-effect light-blue darken-4 btn-small"
          >Request Books</a
        >
        <a class="modal-close waves-effect btn-small">{{
          $t(`text.cancel`)
        }}</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SendRequest",
  mounted() {
    // Initialize Materialize Collapsible Expandible
    var elem = document.querySelector(".collapsible.expandable");
    var instance = M.Collapsible.init(elem, {
      accordion: false,
    });
  },

  created() {
    this.getUserNode();

    this.getGrades();

    if (this.$store.getters.thirdLanguagesObj) {
      this.thirdLanguages = this.$store.getters.thirdLanguagesObj;
    } else {
      this.getThirdLanguages();
    }
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  data() {
    return {
      userNode: {},

      enrollment: [],

      sendPackageObj: { lang: {} },
      beingSentAddedGrades: [],
      beingSentAddedTitles: [],

      errorMessages: [],

      allGrades: [],
      thirdLanguages: [],

      generalInfo: {
        title: "",
        description: "",
        request_by_node: 0,
        request_to_node: 0,
        requested_at: "",
        expected_at: "",

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
          12: false,
        },
      },

      selectedGradeTitles: [],

      addedGrades: [],
      addedTitles: [],
      addTitleDialogInputs: {
        grade: "",
        title: "",
      },
    };
  },

  components: {
    LocalDatePicker: VuePersianDatetimePicker,
  },

  computed: {
    beingSentGrandTotal: function () {
      var grandTotal = 0;

      for (var key in this.sendPackageObj.lang) {
        grandTotal += parseInt(this.sendPackageObj.lang[key]);
      }
      return grandTotal;
    },
  },

  watch: {
    // load titles of the selected grade based on grade change
    "addTitleDialogInputs.grade": function (selectedGrade, previousGrade) {
      this.loadGradeTitles(selectedGrade);
    },

    // update added grades based on added titles change
    addedTitles: function (newAddedTitles) {
      this.updateAddedGrades();
    },
  },

  methods: {
    showEnrollment: function () {
      var instance = this;

      this.$http
        .get(
          this.$http.defaults.emisURL +
            "/enrollment/" +
            this.userNode.description,
          {}
        )
        .then(function (response) {
          console.log(response.data);
          instance.findEnrollmentGrades(response.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    findEnrollmentGrades: function (enrollments) {
      var instance = this;

      var grades = [];
      var gradesObj = [];
      var counter = 0;

      enrollments.forEach(function (enrollment) {
        counter++;
        if (grades.indexOf(enrollment.GradeId) == -1) {
          var grade = {
            GradeId: enrollment.GradeId,
            NoOfPresent: 0,
            NoOfPermanentAbsent: 0,
            NoOfPermanentAbsentOneYear: 0,
            NoOfPermanentAbsentTwoYears: 0,
            NoOfPermanentAbsentThreeYears: 0,
            NoOfPresentRepeaters: 0,
          };

          grades.push(enrollment.GradeId);
          gradesObj.push(grade);
        }

        if (counter == enrollments.length) {
          instance.calculateEnrollmentsByGrade(grades, gradesObj, enrollments);
        }
      });
    },

    calculateEnrollmentsByGrade: function (grades, gradesObj, enrollments) {
      var gradesFinal = gradesObj;

      enrollments.forEach(function (enrollment) {
        gradesFinal.find(function (grade, index) {
          if (enrollment.GradeId == grade.GradeId) {
            gradesFinal[index].NoOfPresent += enrollment.NoOfPresent;
            gradesFinal[index].NoOfPermanentAbsent +=
              enrollment.NoOfPermanentAbsent;
            gradesFinal[index].NoOfPermanentAbsentOneYear +=
              enrollment.NoOfPermanentAbsentOneYear;
            gradesFinal[index].NoOfPermanentAbsentTwoYears +=
              enrollment.NoOfPermanentAbsentTwoYears;
            gradesFinal[index].NoOfPermanentAbsentThreeYears +=
              enrollment.NoOfPermanentAbsentThreeYears;
            gradesFinal[index].NoOfPresentRepeaters +=
              enrollment.NoOfPresentRepeaters;
          }
        });
      });

      console.log(gradesFinal);

      this.enrollment = gradesFinal;
    },

    getUserNode: function () {
      var instance = this;

      this.$http
        .get("/user/nodeandparent", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response.data);

          instance.userNode = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getThirdLanguages: function () {
      var instance = this;

      this.$http
        .get("/meta/languages/third", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response.data);

          instance.thirdLanguages = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    updateAddedGrades: function () {
      var instance = this;

      this.addedGrades = [];

      if (this.addedTitles.length > 0) {
        this.addedTitles.find((title) => {
          var gradeId = title.grade_id;

          var foundGrade = this.allGrades.filter((grade) => {
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
        this.addedGrades.sort(function (a, b) {
          return a.id - b.id;
        });
      }
    },

    sendPackage: function (e) {
      e.preventDefault();

      this.errorMessages = [];

      var instance = this;

      if (
        this.generalInfo.request_by_node != 0 &&
        this.generalInfo.request_to_node != 0 &&
        Object.keys(this.generalInfo.lang).length > 0
      ) {
        let sendPackageObj = {};

        sendPackageObj.title = this.generalInfo.title;
        sendPackageObj.description = this.generalInfo.description;
        sendPackageObj.sent_grades = this.generalInfo.sent_grades;
        sendPackageObj.request_by_node = this.generalInfo.request_by_node.toString();
        sendPackageObj.request_to_node = this.generalInfo.request_to_node.toString();
        sendPackageObj.requested_at = this.generalInfo.requested_at;
        sendPackageObj.expected_at = this.generalInfo.expected_at;

        var cleanedLang = this.generalInfo.lang;

        Object.keys(cleanedLang).forEach(function (key) {
          if (
            cleanedLang[key] == "" ||
            cleanedLang[key] == null ||
            cleanedLang[key] == undefined ||
            cleanedLang[key] == 0
          ) {
            delete cleanedLang[key];
          }
        });

        sendPackageObj.lang = cleanedLang;

        this.addedGrades.forEach(function (grade) {
          sendPackageObj.sent_grades[grade.id] = true;
        });

        // show confirmation dialog
        console.log(sendPackageObj);

        var modalConfirm = document.querySelector("#modalConfirmSend");
        var instance = M.Modal.getInstance(modalConfirm);
        instance.open();

        this.sendPackageObj = sendPackageObj;
        this.confirmSendPackage(sendPackageObj.lang);
        // show confirmation dialog
      } else {
        this.scrollToTop();

        if (this.generalInfo.request_by_node == 0) {
          this.errorMessages.push(
            "Please select a Node that is sending the Request."
          );
        }

        if (this.generalInfo.request_to_node == 0) {
          this.errorMessages.push(
            "Please select a Node to which the Request is being sent."
          );
        }

        if (Object.keys(this.generalInfo.lang).length < 1) {
          this.errorMessages.push(
            "Please add some Titles and their quantities to your Request."
          );
        }
      }
    },

    confirmSendPackage: function (lang) {
      this.beingSentAddedGrades = [];
      this.beingSentAddedTitles = [];

      var instance = this;

      var foundGrades = [];

      for (var key in lang) {
        var title = key.split("_");

        // find the grade object and push to array
        if (!foundGrades.includes(title[2])) {
          foundGrades.push(title[2]);

          this.addedGrades.find(function (grade) {
            if (grade.id == title[2]) {
              instance.beingSentAddedGrades.push(grade);
            }
          });
        }

        // find the title object and push to array
        this.addedTitles.find(function (item) {
          if (item.id == title[0]) {
            var tempItem = {
              ...item,
            };

            if (title[1] == 1) {
              tempItem.language = "Dari";
              tempItem.total = lang[key];
            } else if (title[1] == 2) {
              tempItem.language = "Pashto";
              tempItem.total = lang[key];
            } else {
              instance.thirdLanguages.find(function (language) {
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
      this.beingSentAddedGrades.sort(function (a, b) {
        return a.id - b.id;
      });

      // console.log(this.beingSentAddedGrades);
      // console.log(this.beingSentAddedTitles);
    },

    postPackage: function () {
      var instance = this;
      var sendPackageObj = this.sendPackageObj;

      this.$http
        .post(
          "/requests/send",
          {
            ...sendPackageObj,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          console.log(response.data);
          alert(response.data.message);
          instance.$router.push("/request-books");
        })
        .catch(function (error) {
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

    scrollToTop: function () {
      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    },

    loadGradeTitles: function (selectedGrade) {
      let gradeTitles = this.allGrades.filter((grade) => {
        if (grade.grade.id === selectedGrade) {
          return true;
        }
      });

      this.selectedGradeTitles = gradeTitles[0];
    },

    getGrades: function () {
      var instance = this;

      this.$http
        .get("/meta/grades", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          // console.log(response.data);

          instance.allGrades = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    removeTitle: function (titleObj, gradeId) {
      var deleteConfirmed = confirm(this.$t(`text.confirm_remove_title`));

      if (deleteConfirmed) {
        let index = this.addedTitles.indexOf(titleObj);
        console.log(index);
        this.addedTitles.splice(index, 1);

        // delete values
        delete this.generalInfo.lang[titleObj.id + "_1_" + gradeId];
        delete this.generalInfo.lang[titleObj.id + "_2_" + gradeId];

        var instance = this;
        this.thirdLanguages.forEach(function (lang) {
          delete instance
            .generalInfo.lang[titleObj.id + "_" + lang.id + "_" + gradeId];
        });

        // console.log(this.generalInfo.lang);
      }
    },

    addNewTitle: function () {
      let selectedTitle = {
        grade: this.addTitleDialogInputs.grade,
        title: this.addTitleDialogInputs.title,
      };

      let foundTitle;
      let foundGrade;

      this.allGrades.find((grade) => {
        if (grade.grade.id === selectedTitle.grade) {
          foundGrade = grade.grade;

          grade.titles.find((title) => {
            if (title.id === selectedTitle.title) {
              foundTitle = title;
            }
          });
        }
      });

      if (foundTitle != undefined) {
        // add the title to array if not already added
        let isTitleAdded = this.addedTitles.find((title) => {
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
              foundGrade.title,
            ])
          );
        }
      } else {
        alert(this.$t(`text.select_specific_grade_title`));
      }
    },

    filterTitlesByGrade: function (currentGrade) {
      let filteredByGrade = this.addedTitles.filter((title) => {
        if (title.grade_id == currentGrade) {
          return true;
        }
      });

      return filteredByGrade;
    },

    filterBeingSentTitlesByGrade: function (currentGrade) {
      let filteredByGrade = this.beingSentAddedTitles.filter((title) => {
        if (title.grade_id == currentGrade) {
          return true;
        }
      });

      return filteredByGrade;
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