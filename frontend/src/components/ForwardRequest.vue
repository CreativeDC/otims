<template>
  <div>
    <div class="container" id="forwardRequestPage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Approved Requests</span>

              <table class="striped centered">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Title</th>
                    <th>Request Date</th>
                    <th>By</th>
                    <th>To</th>
                    <th>{{$t(`text.status`)}}</th>
                    <th>{{$t(`text.action`)}}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="req in approvedRequests.data" v-bind:key="req.id">
                    <td>{{req.id}}</td>
                    <td>{{req.title}}</td>
                    <td>{{moment(req.requested_at, 'YYYY-M-D').format('jYYYY-jM-jD')}}</td>
                    <td>{{req.request_by_node}}</td>
                    <td>{{req.request_to_node}}</td>

                    <td>
                      <div v-if="req.approved == 1" class="receivedStatus">Approved</div>
                      <div v-if="req.merged == 1" class="otherStatus">Merged</div>
                      <div v-if="req.delivered == 1" class="otherStatus">Delivered</div>
                      <div v-if="req.approved == 0" class="pendingStatus">Pending</div>
                    </td>

                    <td>
                      <button
                        v-if="!generalInfo.merged_requests.includes(req.id)"
                        @click="mergeRequest(req)"
                        class="waves-effect btn-small"
                      >Merge</button>
                      <button
                        class="waves-effect light-blue darken-4 btn-small modal-trigger"
                        href="#modalRequestDetails"
                        @click="viewRequestDetails(req)"
                      >Details</button>
                    </td>
                  </tr>

                  <tr v-show="!approvedRequests.data || !approvedRequests.data.length > 0">
                    <td>No approved pending requests available.</td>
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

          <div v-if="generalInfo.merged_requests.length > 0" class="card">
            <div class="card-content">
              <span class="card-title">Merge & Forward Requests</span>

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

          <ul v-show="generalInfo.merged_requests.length > 0" class="collapsible expandable">
            <li class="active">
              <div class="collapsible-header">
                <i class="material-icons" style="color: #01579B;">info</i>
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
                    <select id="RequestBy" name="RequestBy" v-model="generalInfo.request_by_node">
                      <option value disabled selected>Select Node</option>
                      <option v-if="userNode.id" :value="userNode.id">{{userNode.title}}</option>
                    </select>
                  </div>
                  <br />

                  <div class="form-field">
                    <label for="RequestTo">Request To</label>
                    <select id="RequestTo" name="RequestTo" v-model="generalInfo.request_to_node">
                      <option value disabled selected>Select Node</option>
                      <option
                        v-if="userNode.parent"
                        :value="userNode.parent.id"
                      >{{userNode.parent.title}}</option>
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
                          prevMonth: 'تېره مياشت/ماه قبل'
                        }
                      },
                      en: {
                        lang: {
                          label: 'Gregorian / میلادی'
                        }
                      }}"
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
                          prevMonth: 'تېره مياشت/ماه قبل'
                        }
                      },
                      en: {
                        lang: {
                          label: 'Gregorian / میلادی'
                        }
                      }}"
                    ></local-date-picker>
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
                  href="#modalAddTitle2"
                >{{$t(`text.add_title`)}}</button>

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
                              v-model="generalInfo.lang[title.id + '_' + title.thirdLang + '_' +grade.id]"
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
                Request Books
              </div>
              <div class="collapsible-body">
                <button
                  class="waves-effect light-blue darken-4 btn-small"
                  form="generalForm"
                  type="submit"
                >Request Books</button>

                <p
                  class="grey-text lighten-1"
                >* Please make sure that you have reviewed all the information you have entered before clicking the Request Books button.</p>
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

    <div id="modalConfirmSend" class="modal">
      <div class="modal-content">
        <h4>Confirm Request Details</h4>
        <p>Please review the following and make sure they are correct before pressing the Request Books button.</p>

        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">Request Title</td>
              <td>{{sendPackageObj.title}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request Description</td>
              <td>{{sendPackageObj.description}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request By</td>
              <td v-if="userNode.id">{{userNode.title}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request To</td>
              <td v-if="userNode.parent">{{userNode.parent.title}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request Date</td>
              <td
                v-if="sendPackageObj.requested_at"
              >{{moment(sendPackageObj.requested_at, 'YYYY/M/D').format('jYYYY-jM-jD')}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Expected Date</td>
              <td
                v-if="sendPackageObj.expected_at"
              >{{moment(sendPackageObj.expected_at, 'YYYY/M/D').format('jYYYY-jM-jD')}}</td>
              <td></td>
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
        >Request Books</a>
        <a class="modal-close waves-effect btn-small">{{$t(`text.cancel`)}}</a>
      </div>
    </div>

    <!-- Details Modal Structure -->
    <div id="modalRequestDetails" class="modal modal-fixed-footer">
      <div class="modal-content">
        <h4>Request Details</h4>
        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">Request Title</td>
              <td>{{selectedRequestView.request.title}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request Description</td>
              <td>{{selectedRequestView.request.description}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request Date</td>
              <td>
                {{selectedRequestView.request.requested_at == null ? "" :
                moment(selectedRequestView.request.requested_at, 'YYYY-M-D').format('jYYYY-jM-jD')
                }} ({{selectedRequestView.request.requested_at}})
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Expected Date</td>
              <td>
                {{selectedRequestView.request.expected_at == null ? "" :
                moment(selectedRequestView.request.expected_at, 'YYYY-M-D').format('jYYYY-jM-jD')
                }} ({{selectedRequestView.request.expected_at}})
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request By</td>
              <td>{{selectedRequestView.request.request_by_node}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Request To</td>
              <td>{{selectedRequestView.request.request_to_node}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Remarks</td>
              <td style="font-size: 10px;">
                <b>Approval Remarks</b>
                {{selectedRequestView.allDetails.request.approved_description}}
                <br />
                <b>Merge Remarks</b>
                {{selectedRequestView.allDetails.request.merged_description}}
                <br />
                <b>Delivery Remarks</b>
                {{selectedRequestView.allDetails.request.delivered_description}}
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.current_status`)}}</td>

              <td>
                <div
                  v-if="selectedRequestView.request.approved == 1"
                  class="receivedStatus"
                >Approved</div>
                <div v-if="selectedRequestView.request.merged == 1" class="otherStatus">Merged</div>
                <div v-if="selectedRequestView.request.delivered == 1" class="otherStatus">Delivered</div>
                <div v-if="selectedRequestView.request.approved == 0" class="pendingStatus">Pending</div>
              </td>
            </tr>
          </tbody>
        </table>

        <table class="centered">
          <thead>
            <tr>
              <th>{{$t(`text.grade`)}}</th>
              <th>{{$t(`text.title`)}}</th>
              <th>{{$t(`text.language`)}}</th>
              <th>{{$t(`text.quantity`)}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(grade, index) in selectedRequestView.allDetails.details" v-bind:key="index">
              <td style="border: 1px solid #e2e2e2;">{{index}}</td>
              <td style="border: 1px solid #e2e2e2;">
                <tr v-for="(title, index) in grade" style="border: none;" v-bind:key="index">
                  <td>{{title.title}}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2;">
                <tr v-for="(title, index) in grade" style="border: none;" v-bind:key="index">
                  <td>{{title.language}}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2;">
                <tr v-for="(title, index) in grade" style="border: none;" v-bind:key="index">
                  <td>{{title.total}}</td>
                </tr>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small">{{$t(`text.close`)}}</a>
      </div>
    </div>
  </div>
</template>
          

<script>
export default {
  name: "ForwardRequest",
  mounted() {
    // Initialize Materialize Collapsible Expandible
    var elem = document.querySelector(".collapsible.expandable");
    var instance = M.Collapsible.init(elem, {
      accordion: false
    });

    this.getApprovedRequests();
  },

  beforeDestroy() {
    this.$store.commit("setForwardRequest", null);
    console.log("ForwardRequest set to null in state store.");
  },

  created() {
    this.getThirdLanguages();
    this.getUserNode();
    this.getGrades();

    if (this.$store.getters.forwardRequestObj) {
      var temp = this.$store.getters.forwardRequestObj;
      this.selectedRequest = temp;
      console.log(this.selectedRequest);
    } else {
      console.log("Select a request to forward.");
    }
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  data() {
    return {
      userNode: {},

      sendPackageObj: { lang: {} },
      beingSentAddedGrades: [],
      beingSentAddedTitles: [],

      errorMessages: [],

      approvedRequests: {},

      allGrades: [],
      thirdLanguages: [],

      selectedRequestView: {
        type: "",
        request: {},
        allDetails: {
          request: {},
          detail: {}
        }
      },

      selectedRequest: {
        type: "",
        request: {},
        allDetails: {
          request: {},
          detail: {}
        }
      },

      sentLang: {},

      generalInfo: {
        title: "",
        description: "fwd: requests merged",
        request_by_node: 0,
        request_to_node: 0,
        requested_at: "",
        expected_at: "",

        merged_requests: [],

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
    LocalDatePicker: VuePersianDatetimePicker
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

  computed: {
    storeForwardRequestSet: function() {
      if (this.$store.getters.forwardRequestObj) {
        return true;
      }

      return false;
    },

    beingSentGrandTotal: function() {
      var grandTotal = 0;

      for (var key in this.sendPackageObj.lang) {
        grandTotal += parseInt(this.sendPackageObj.lang[key]);
      }
      return grandTotal;
    }
  },

  methods: {
    getUserNode: function() {
      var instance = this;

      this.$http
        .get("/user/nodeandparent", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.userNode = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
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

    loadSentQuantities: function() {
      // console.log(this.sentLang);
      var tempSentLang = this.sentLang;

      // empty the entered values in fine
      for (var key in this.generalInfo.lang) {
        this.$set(this.generalInfo.lang, key, 0);
      }

      // load sent quantities
      for (var key in tempSentLang) {
        this.$set(this.generalInfo.lang, key, tempSentLang[key]);
      }
    },

    getApprovedRequests: function() {
      var instance = this;

      this.$http
        .get("/requests/received/approved", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.approvedRequests.data = response.data;
          instance.$forceUpdate();
          console.log(instance.approvedRequests);
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    generateForwardForm: function() {
      console.log("Generating forward form");
      console.log(this.selectedRequest);

      var gradesTitlesObj = this.selectedRequest.allDetails.details;
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
                    var langKey = foundTitle.id + "_1_" + foundTitle.grade_id;

                    instance.sentLang[langKey] = String(
                      isNaN(parseInt(instance.sentLang[langKey]))
                        ? 0 + addedTitle.total
                        : parseInt(instance.sentLang[langKey]) +
                            addedTitle.total
                    );
                  } else if (addedTitle.language === "Pashto") {
                    var langKey = foundTitle.id + "_2_" + foundTitle.grade_id;
                    instance.sentLang[langKey] = String(
                      isNaN(parseInt(instance.sentLang[langKey]))
                        ? 0 + addedTitle.total
                        : parseInt(instance.sentLang[langKey]) +
                            addedTitle.total
                    );
                  } else {
                    let foundLang;
                    instance.thirdLanguages.find(function(lang) {
                      if (lang.title == addedTitle.language) {
                        foundLang = lang;
                      }
                    });

                    var langKey =
                      foundTitle.id +
                      "_" +
                      foundLang.id +
                      "_" +
                      foundTitle.grade_id;

                    instance.sentLang[langKey] = String(
                      isNaN(parseInt(instance.sentLang[langKey]))
                        ? 0 + addedTitle.total
                        : parseInt(instance.sentLang[langKey]) +
                            addedTitle.total
                    );
                  }
                }
              });
            });
          }
        });
      });

      this.loadSentQuantities();
      this.generalInfo.merged_requests.push(this.selectedRequest.request.id);
      this.generalInfo.description += " - " + this.selectedRequest.request.id;
      console.log(this.generalInfo);
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
        sendPackageObj.merged_requests = this.generalInfo.merged_requests;

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

        sendPackageObj.lang = cleanedLang;

        this.addedGrades.forEach(function(grade) {
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

      // console.log(this.beingSentAddedGrades);
      // console.log(this.beingSentAddedTitles);
    },

    postPackage: function() {
      var instance = this;
      var sendPackageObj = this.sendPackageObj;

      this.$http
        .post(
          "/requests/send",
          {
            ...sendPackageObj
          },
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
          instance.$router.push("/request-books");
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
          instance.allGrades = response.data;

          if (instance.storeForwardRequestSet) {
            instance.generateForwardForm();
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
        delete this.generalInfo.lang[titleObj.id + "_2_" + gradeId];

        var instance = this;
        this.thirdLanguages.forEach(function(lang) {
          delete instance
            .generalInfo.lang[titleObj.id + "_" + lang.id + "_" + gradeId];
        });
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
    },

    filterBeingSentTitlesByGrade: function(currentGrade) {
      let filteredByGrade = this.beingSentAddedTitles.filter(title => {
        if (title.grade_id == currentGrade) {
          return true;
        }
      });

      return filteredByGrade;
    },

    viewRequestDetails: function(req) {
      this.selectedRequestView.request = req;

      this.getViewRequestDetails(req.id);
    },

    getViewRequestDetails: function(id) {
      var instance = this;

      this.$http
        .get("/requests/" + id, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.selectedRequestView.allDetails = response.data;
          console.log(instance.selectedRequestView);
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    mergeRequest(req) {
      this.selectedRequest.request = req;
      this.selectedRequest.allDetails = { request: {}, detail: {} };

      this.getMergeRequestDetails(req.id);
    },

    getMergeRequestDetails: function(id) {
      var instance = this;

      this.$http
        .get("/requests/" + id, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.selectedRequest.allDetails = response.data;
          console.log(instance.selectedRequest);

          instance.generateForwardForm();
        })
        .catch(function(error) {
          console.log(error);
        });
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