<template>
  <div>
    <div class="container" id="requestBooksPage" style="width: 100%">
      <div class="row">
        <div class="col s12">
          <div class="card" style="width: 100%">
            <div class="card-content">
              <span class="card-title">Sent Requests</span>

              <table class="striped centered">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Title</th>
                    <th>Request Date</th>
                    <th>By</th>
                    <th>To</th>
                    <th>{{ $t(`text.status`) }}</th>
                    <th>{{ $t(`text.action`) }}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="req in sentRequests.data" v-bind:key="req.id">
                    <td>{{ req.id }}</td>
                    <td>{{ req.title }}</td>
                    <td>
                      {{
                        moment(req.requested_at, "YYYY-M-D").format(
                          "jYYYY-jM-jD"
                        )
                      }}
                    </td>
                    <td>{{ req.request_by_node }}</td>
                    <td>{{ req.request_to_node }}</td>

                    <td>
                      <div v-if="req.approved == 1" class="receivedStatus">
                        Approved
                      </div>
                      <div v-if="req.merged == 1" class="otherStatus">
                        Merged
                      </div>
                      <div v-if="req.delivered == 1" class="otherStatus">
                        Delivered
                      </div>
                      <div v-if="req.approved == 0" class="pendingStatus">
                        Pending
                      </div>
                    </td>

                    <td>
                      <button
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalRequestDetails"
                        @click="viewRequestDetails(req, 'sent')"
                      >
                        {{ $t(`text.view_details`) }}
                      </button>
                    </td>
                  </tr>

                  <tr
                    v-show="!sentRequests.data || !sentRequests.data.length > 0"
                  >
                    <td>No requests sent yet.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>

              <br />
              <router-link
                to="/send-request"
                class="waves-effect light-blue darken-4 btn-small"
                id="panelNewRequestBtn"
                >Send New Request</router-link
              >

              <a class="waves-effect btn-small" @click="loadMoreSentRequests">{{
                $t(`text.view_more`)
              }}</a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12">
          <div class="card" style="width: 100%">
            <div class="card-content">
              <span class="card-title">Received Requests</span>

              <table class="striped centered">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Title</th>
                    <th>Request Date</th>
                    <th>By</th>
                    <th>To</th>
                    <th>{{ $t(`text.status`) }}</th>
                    <th>{{ $t(`text.action`) }}</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="req in receivedRequests.data" v-bind:key="req.id">
                    <td>{{ req.id }}</td>
                    <td>{{ req.title }}</td>
                    <td>
                      {{
                        moment(req.requested_at, "YYYY-M-D").format(
                          "jYYYY-jM-jD"
                        )
                      }}
                    </td>
                    <td>{{ req.request_by_node }}</td>
                    <td>{{ req.request_to_node }}</td>

                    <td>
                      <div v-if="req.approved == 1" class="receivedStatus">
                        Approved
                      </div>
                      <div v-if="req.merged == 1" class="otherStatus">
                        Merged
                      </div>
                      <div v-if="req.delivered == 1" class="otherStatus">
                        Delivered
                      </div>
                      <div v-if="req.approved == 0" class="pendingStatus">
                        Pending
                      </div>
                    </td>

                    <td>
                      <button
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalRequestDetails"
                        @click="viewRequestDetails(req, 'received')"
                      >
                        {{ $t(`text.view_details`) }}
                      </button>
                    </td>
                  </tr>

                  <tr
                    v-show="
                      !receivedRequests.data ||
                      !receivedRequests.data.length > 0
                    "
                  >
                    <td>No requests received yet.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>

              <br />
              <router-link
                to="/forward-request"
                class="waves-effect light-blue darken-4 btn-small"
                id="panelReceiveRequestBtn"
                >Merge & Forward Requests</router-link
              >

              <a
                class="waves-effect btn-small"
                @click="loadMoreReceivedRequests"
                >{{ $t(`text.view_more`) }}</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Structure -->
    <div id="modalEnrollment" class="modal">
      <div class="modal-content">
        <h4>Enrollment Details</h4>
        <p>
          The latest available enrollment details for
          {{ enrollmentNode.title }} school are shown below.
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

    <!-- Details Modal Structure -->
    <div id="modalRequestDetails" class="modal modal-fixed-footer">
      <div class="modal-content">
        <h4>Request Details</h4>
        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold">Request Title</td>
              <td>{{ selectedRequest.request.title }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request Description</td>
              <td>{{ selectedRequest.request.description }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request Date</td>
              <td>
                {{
                  selectedRequest.request.requested_at == null
                    ? ""
                    : moment(
                        selectedRequest.request.requested_at,
                        "YYYY-M-D"
                      ).format("jYYYY-jM-jD")
                }}
                ({{ selectedRequest.request.requested_at }})
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold">Expected Date</td>
              <td>
                {{
                  selectedRequest.request.expected_at == null
                    ? ""
                    : moment(
                        selectedRequest.request.expected_at,
                        "YYYY-M-D"
                      ).format("jYYYY-jM-jD")
                }}
                ({{ selectedRequest.request.expected_at }})
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request By</td>
              <td>
                {{ selectedRequest.request.request_by_node }}

                <a
                  v-show="selectedRequest.allDetails.sender.level_id == 15"
                  class="waves-effect modal-close light-blue darken-4 btn-small modal-trigger"
                  href="#modalEnrollment"
                  @click="showEnrollment(selectedRequest.allDetails.sender)"
                  >View Enrollment</a
                >
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold">Request To</td>
              <td>{{ selectedRequest.request.request_to_node }}</td>
            </tr>

            <tr>
              <td style="font-weight: bold">Remarks</td>
              <td style="font-size: 10px">
                <b>Approval Remarks</b>
                {{ selectedRequest.allDetails.request.approved_description }}
                <br />
                <b>Merge Remarks</b>
                {{ selectedRequest.allDetails.request.merged_description }}
                <br />
                <b>Delivery Remarks</b>
                {{ selectedRequest.allDetails.request.delivered_description }}
              </td>
            </tr>

            <tr>
              <td style="font-weight: bold">{{ $t(`text.current_status`) }}</td>

              <td>
                <div
                  v-if="selectedRequest.request.approved == 1"
                  class="receivedStatus"
                >
                  Approved
                </div>
                <div
                  v-if="selectedRequest.request.merged == 1"
                  class="otherStatus"
                >
                  Merged
                </div>
                <div
                  v-if="selectedRequest.request.delivered == 1"
                  class="otherStatus"
                >
                  Delivered
                </div>
                <div
                  v-if="selectedRequest.request.approved == 0"
                  class="pendingStatus"
                >
                  Pending
                </div>
              </td>
            </tr>

            <tr v-if="selectedRequest.type == 'received'">
              <td style="font-weight: bold">Action</td>
              <td>
                <a
                  v-if="selectedRequest.request.approved == 0"
                  class="waves-effect modal-close light-blue darken-4 btn-small"
                  style="margin-top: 5px; text-transform: none"
                  @click="showApprovalDialog"
                  >Approve</a
                >

                <button
                  v-if="
                    selectedRequest.request.approved == 1 &&
                    selectedRequest.request.merged == 0 &&
                    selectedRequest.request.delivered == 0
                  "
                  class="waves-effect modal-close light-blue darken-4 btn-small"
                  style="margin-top: 5px; text-transform: none"
                  @click="loadForwardRequestPage"
                >
                  Forward
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div style="margin-top: 10px; margin-bottom: 20px">
          <table class="striped">
            <thead>
              <tr
                style="
                  color: #ee6e73;
                  font-size: 14px !important;
                  text-transform: uppercase;
                  font-weight: normal !important;
                "
              >
                <td>{{ $t(`text.document_title`) }}</td>
                <td>{{ $t(`text.download`) }}</td>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(doc, index) in selectedRequestDocs.request_docs"
                v-bind:key="index"
              >
                <td>{{ doc.title }}</td>
                <td>
                  <a
                    class="btn btn-small"
                    @click="downloadRequestDocument(doc.id)"
                    >{{ $t(`text.download`) }}</a
                  >
                </td>
              </tr>

              <tr v-if="selectedRequestDocs.request_docs.length < 1">
                <td>{{ $t(`text.no_uploaded_documents`) }}</td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <a
            class="modal-close waves-effect btn-small light-blue darken-4"
            @click="showUploadDialog"
            >{{ $t(`text.upload_new`) }}</a
          >
        </div>

        <table class="centered">
          <thead>
            <tr>
              <th>{{ $t(`text.grade`) }}</th>
              <th>{{ $t(`text.title`) }}</th>
              <th>{{ $t(`text.language`) }}</th>
              <th>{{ $t(`text.quantity`) }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(grade, index) in selectedRequest.allDetails.details"
              v-bind:key="index"
            >
              <td style="border: 1px solid #e2e2e2">{{ index }}</td>
              <td style="border: 1px solid #e2e2e2">
                <tr
                  v-for="(title, index) in grade"
                  style="border: none"
                  v-bind:key="index"
                >
                  <td>{{ title.title }}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2">
                <tr
                  v-for="(title, index) in grade"
                  style="border: none"
                  v-bind:key="index"
                >
                  <td>{{ title.language }}</td>
                </tr>
              </td>

              <td style="border: 1px solid #e2e2e2">
                <tr
                  v-for="(title, index) in grade"
                  style="border: none"
                  v-bind:key="index"
                >
                  <td>{{ title.total }}</td>
                </tr>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small">{{
          $t(`text.close`)
        }}</a>
      </div>
    </div>

    <!-- Upload Document Modal Structure -->
    <div id="modalUploadDocument" class="modal">
      <div class="modal-content">
        <h4>{{ $t(`text.upload_document`) }}</h4>

        <div v-if="!showUploadProgress" style="margin-top: 20px">
          <div class="row">
            <div class="form-field">
              <label for="documentTitle">{{ $t(`text.document_title`) }}</label>
              <input
                type="text"
                id="documentTitle"
                name="documentTitle"
                :placeholder="$t(`text.document_title`)"
                class="validate"
                required
                aria-required="true"
                v-model="uploadDoc.title"
              />
            </div>
          </div>

          <div class="row">
            <div class="file-field input-field">
              <div class="btn-small">
                <span>{{ $t(`text.select_file`) }}</span>
                <input
                  type="file"
                  id="file"
                  ref="file"
                  v-on:change="handleFileUpload()"
                  required
                />
              </div>
              <div class="file-path-wrapper">
                <input class="file-path validate" type="text" />
              </div>
            </div>
          </div>
        </div>

        <div v-if="showUploadProgress" style="margin-top: 20px">
          <p>{{ $t(`text.uploading_document_wait`) }}</p>
          <div class="progress">
            <div class="indeterminate"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button
          class="waves-effect light-blue darken-4 btn-small"
          @click="uploadRequestDocument"
        >
          {{ $t(`text.upload_document`) }}
        </button>
        <a class="modal-close waves-effect btn-small">{{ $t(`text.close`) }}</a>
      </div>
    </div>

    <!-- Approve Request Modal Structure -->
    <div id="modalApproveRequest" class="modal">
      <div class="modal-content">
        <h4>Approve Request</h4>

        <div style="margin-top: 20px">
          <div class="row">
            <div class="form-field">
              <label for="approvalRemarks">Approval Remarks</label>
              <input
                type="text"
                id="approvalRemarks"
                name="approvalRemarks"
                placeholder="Approval Remarks"
                class="validate"
                required
                aria-required="true"
                v-model="approvalRemarks"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button
          class="waves-effect light-blue darken-4 btn-small"
          @click="approveRequest"
        >
          Approve Request
        </button>
        <a class="modal-close waves-effect btn-small">{{ $t(`text.close`) }}</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RequestBooks",
  mounted() {
    this.getSentRequests();
    this.getReceivedRequests();
  },

  data() {
    return {
      enrollment: [],
      enrollmentNode: {},

      sentRequests: {},
      receivedRequests: {},

      selectedRequest: {
        type: "",
        request: {},
        allDetails: {
          request: {},
          detail: {},
          sender: {},
        },
      },

      uploadDoc: {
        title: "",
        file: "",
      },

      showUploadProgress: false,
      selectedRequestDocs: { request_docs: [] },

      approvalRemarks: "",
    };
  },

  methods: {
    showEnrollment: function (node) {
      var instance = this;
      this.enrollmentNode = node;

      this.$http
        .get(
          this.$http.defaults.emisURL + "/enrollment/" + node.description,
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

    loadForwardRequestPage() {
      this.$store.commit("setForwardRequest", this.selectedRequest);
      this.$router.push("/forward-request");
    },

    handleFileUpload() {
      this.uploadDoc.file = this.$refs.file.files[0];
    },

    uploadRequestDocument: function () {
      if (this.uploadDoc.title != "") {
        this.uploadRequestDocumentNow();
      }
    },

    uploadRequestDocumentNow: function () {
      var instance = this;
      this.showUploadProgress = true;

      let request_doc = new FormData();
      request_doc.append("request_doc[file][0]", this.uploadDoc.file);
      request_doc.append(
        "request_doc[request_id]",
        this.selectedRequest.request.id
      );
      request_doc.append("request_doc[title]", this.uploadDoc.title);

      this.$http
        .post("/requests/documents/store", request_doc, {
          params: this.$http.defaults.params,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(function (response) {
          console.log(response.data);
          alert(response.data.message);
          instance.showUploadProgress = false;
          instance.hideUploadDialog();
        })
        .catch(function (error) {
          console.log(error.response);
          instance.showUploadProgress = false;
          alert(this.$t(`text.upload_document_error`));
        });
    },

    downloadRequestDocument: function (fileId) {
      var instance = this;

      this.$http
        .get("/requests/documents/" + fileId, {
          responseType: "blob",
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          instance.forceFileDownload(response, fileId);
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    forceFileDownload: function (response, fileId) {
      const url = window.URL.createObjectURL(response.data);
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "document_" + fileId);
      link.click();
    },

    getRequestDocuments: function (requestId) {
      var instance = this;

      this.selectedRequestDocs.request_docs = [];

      this.$http
        .get("/requests/" + requestId + "/documents", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response.data);
          instance.selectedRequestDocs = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    showUploadDialog: function () {
      this.uploadDoc.title = "";
      this.uploadDoc.file = "";

      this.showUploadProgress = false;

      var modalUpload = document.querySelector("#modalUploadDocument");
      var instance = M.Modal.getInstance(modalUpload);
      instance.open();
    },

    hideUploadDialog: function () {
      var modalUpload = document.querySelector("#modalUploadDocument");
      var instance = M.Modal.getInstance(modalUpload);
      instance.close();
    },

    showApprovalDialog: function () {
      var modalApproval = document.querySelector("#modalApproveRequest");
      var instance = M.Modal.getInstance(modalApproval);
      instance.open();
    },

    hideApprovalDialog: function () {
      var modalApproval = document.querySelector("#modalApproveRequest");
      var instance = M.Modal.getInstance(modalApproval);
      instance.close();
    },

    approveRequest: function () {
      var instance = this;

      var approveObj = {
        request_id: this.selectedRequest.request.id,
        approved_description: this.approvalRemarks,
      };

      this.$http
        .post(
          "/requests/approve",
          {
            ...approveObj,
          },
          {
            params: this.$http.defaults.params,
          }
        )
        .then(function (response) {
          console.log(response.data);
          instance.hideApprovalDialog();
          alert("Request has been successfully approved.");

          // instance.$router.push("/request-books");
        })
        .catch(function (error) {
          console.log(error.response);
          alert("The Request could not be approved. Please try again.");
        });
    },

    getReceivedRequests: function () {
      var instance = this;

      this.$http
        .get("/requests/received?page=1", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response.data);

          instance.receivedRequests = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    getSentRequests: function () {
      var instance = this;

      this.$http
        .get("/requests/sent?page=1", {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          console.log(response.data);

          instance.sentRequests = response.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    loadMoreReceivedRequests: function () {
      var nextUrl = this.receivedRequests.next_page_url;
      nextUrl = nextUrl.substring(1, nextUrl.length);

      var instance = this;

      if (nextUrl != null) {
        this.$http
          .get("/requests/received" + nextUrl, {
            params: this.$http.defaults.params,
          })
          .then(function (response) {
            console.log(response.data);

            let prevRecords = instance.receivedRequests.data;
            let newRecords = response.data.data;

            instance.receivedRequests = response.data;

            instance.pushNewReceivedRequests(prevRecords, newRecords);
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    },

    pushNewReceivedRequests: function (prevRecords, newRecords) {
      this.receivedRequests.data = prevRecords;

      for (var key in newRecords) {
        this.receivedRequests.data.push(newRecords[key]);
      }
    },

    loadMoreSentRequests: function () {
      var nextUrl = this.sentRequests.next_page_url;
      nextUrl = nextUrl.substring(1, nextUrl.length);

      var instance = this;

      if (nextUrl != null) {
        this.$http
          .get("/requests/sent" + nextUrl, {
            params: this.$http.defaults.params,
          })
          .then(function (response) {
            console.log(response.data);

            let prevRecords = instance.sentRequests.data;
            let newRecords = response.data.data;

            instance.sentRequests = response.data;

            instance.pushNewSentRequests(prevRecords, newRecords);
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    },

    pushNewSentRequests: function (prevRecords, newRecords) {
      this.sentRequests.data = prevRecords;

      for (var key in newRecords) {
        this.sentRequests.data.push(newRecords[key]);
      }
    },

    viewRequestDetails: function (req, type) {
      this.selectedRequest.type = type;
      this.selectedRequest.request = req;
      this.selectedRequestDocs = [];

      this.getRequestDetails(req.id);
      this.getRequestDocuments(req.id);
    },

    getRequestDetails: function (id) {
      var instance = this;

      this.$http
        .get("/requests/" + id, {
          params: this.$http.defaults.params,
        })
        .then(function (response) {
          instance.selectedRequest.allDetails = response.data;
          console.log(instance.selectedRequest);
        })
        .catch(function (error) {
          console.log(error);
        });
    },
  },
};
</script>


<style scoped>
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

.otherStatus {
  text-align: center;
  font-size: 12px;
  border-radius: 3px;
  color: #fff;
  background-color: #d0dd18;
  padding: 1px;
  width: 70px;
}
</style>