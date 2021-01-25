<template>
  <div>
    <div class="container" id="homePage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">{{$t(`text.sent_packages`)}}</span>

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
                  <tr v-for="pckg in sentPackages.data" v-bind:key="pckg.id">
                    <td>{{pckg.id}}</td>
                    <td>
                    <span v-if="pckg.docs > 0" class="material-icons" style="color: green;">check</span>
                    <span v-if="pckg.docs <= 0" class="material-icons" style="color: red;">close</span>
                    </td>
                    <td>{{pckg.title}}</td>
                    <td>{{moment(pckg.send_date, 'YYYY-M-D').format('jYYYY-jM-jD')}}</td>
                    <td>{{pckg.from_node}}</td>
                    <td>{{pckg.to_node}}</td>
                    <td v-if="pckg.recieved_status == 1">
                      <div class="receivedStatus">{{$t(`text.received`)}}</div>
                    </td>

                    <td v-if="pckg.recieved_status == 0">
                      <div class="pendingStatus">{{$t(`text.pending`)}}</div>
                    </td>
                    <td>
                      <button
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalSentShipmentDetails"
                        @click="viewSentPackageDetails(pckg)"
                      >{{$t(`text.view_details`)}}</button>
                    </td>
                  </tr>

                  <tr v-show="!sentPackages.data || !sentPackages.data.length > 0">
                    <td>{{$t(`text.no_sent_package`)}}</td>
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
                to="/send-package"
                class="waves-effect light-blue darken-4 btn-small"
                id="panelNewPackageBtn"
              >{{$t(`text.send_new_package`)}}</router-link>
              <a
                class="waves-effect btn-small"
                @click="loadMoreSentPackages"
              >{{$t(`text.view_more`)}}</a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12">
          <div class="card" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">{{$t(`text.received_packages`)}}</span>

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
                        @click="viewRecvdPackageDetails(pckg)"
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalRecvdShipmentDetails"
                      >{{$t(`text.view_details`)}}</button>
                    </td>
                  </tr>

                  <tr v-show="!receivedPackages.data || !receivedPackages.data.length > 0">
                    <td>{{$t(`text.no_received_package`)}}</td>
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
                to="/receive-package"
                class="waves-effect light-blue darken-4 btn-small"
                id="panelReceivePackageBtn"
              >{{$t(`text.view_pending_packages`)}}</router-link>

              <a
                class="waves-effect btn-small"
                @click="loadMoreReceivedPackages"
              >{{$t(`text.view_more`)}}</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sent Modal Structure -->
    <div id="modalSentShipmentDetails" class="modal modal-fixed-footer">
      <div class="modal-content">
        <h4>{{$t(`text.shipment_details`)}}</h4>
        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_title`)}}</td>
              <td>{{sentSelectedPackage.package.title}}</td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_received`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.total_general}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_description`)}}</td>
              <td>{{sentSelectedPackage.package.description}}</td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_safe`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.total_safe}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.sent_date`)}}</td>
              <td>
                {{sentSelectedPackage.package.send_date == null ? "" :
                moment(sentSelectedPackage.package.send_date, 'YYYY-M-D').format('jYYYY-jM-jD')
                }} ({{sentSelectedPackage.package.send_date}})
              </td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_damaged`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.damaged}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.received_date`)}}</td>
              <td>
                {{sentSelectedPackage.package.receive_date == null ? "" :
                moment(sentSelectedPackage.package.receive_date, 'YYYY-M-D').format('jYYYY-jM-jD')
                }} ({{sentSelectedPackage.package.receive_date}})
              </td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_lost`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.lost}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.from`)}}</td>
              <td>{{sentSelectedPackage.package.from_node}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.to`)}}</td>
              <td>{{sentSelectedPackage.package.to_node}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.sender_name`)}}</td>
              <td>{{sentSelectedPackage.package.sender_name}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.current_status`)}}</td>
              <td v-if="sentSelectedPackage.package.recieved_status == 1">
                <div class="receivedStatus">{{$t(`text.received`)}}</div>
              </td>

              <td v-if="sentSelectedPackage.package.recieved_status == 0">
                <div class="pendingStatus">{{$t(`text.pending`)}}</div>
              </td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <div style="margin-top: 10px; margin-bottom: 20px;">
          <table class="striped">
            <thead>
              <tr
                style="color: #ee6e73; font-size: 14px !important; text-transform: uppercase; font-weight: normal !important;"
              >
                <td>{{$t(`text.document_title`)}}</td>
                <td>{{$t(`text.type`)}}</td>
                <td>{{$t(`text.download`)}}</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, index) in selectedPackageDocs.sent_docs" v-bind:key="index">
                <td>{{doc.title}}</td>
                <td>{{$t(`text.sent`)}}</td>
                <td>
                  <a
                    class="btn btn-small"
                    @click="downloadShipmentDocument(doc.id)"
                  >{{$t(`text.download`)}}</a>
                </td>
              </tr>

              <tr v-for="(doc, index) in selectedPackageDocs.reciept_docs" v-bind:key="index+4520">
                <td>{{doc.title}}</td>
                <td>{{$t(`text.received`)}}</td>
                <td>
                  <a
                    class="btn btn-small"
                    @click="downloadShipmentDocument(doc.id)"
                  >{{$t(`text.download`)}}</a>
                </td>
              </tr>

              <tr
                v-if="selectedPackageDocs.reciept_docs.length < 1 && selectedPackageDocs.sent_docs.length < 1"
              >
                <td>{{$t(`text.no_uploaded_documents`)}}</td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <a
            class="modal-close waves-effect btn-small light-blue darken-4"
            @click="showUploadDialog"
          >{{$t(`text.upload_new`)}}</a>
        </div>

        <div class="row" style="margin-top: 10px;">
          <div class="col s12">
            <ul class="tabs">
              <li class="tab col s3">
                <a class="active" href="#sent">{{$t(`text.sent`)}}</a>
              </li>
              <li class="tab col s3">
                <a href="#received">{{$t(`text.received`)}}</a>
              </li>
              <li class="tab col s3">
                <a href="#damaged">{{$t(`text.damaged`)}}</a>
              </li>
              <li class="tab col s3">
                <a href="#lost">{{$t(`text.lost`)}}</a>
              </li>
            </ul>
          </div>
          <div id="sent" class="col s12">
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
                <tr
                  v-for="(grade, index) in sentSelectedPackage.allDetails.sent_detail"
                  v-bind:key="index"
                >
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

          <div id="received" class="col s12">
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
                <tr
                  v-for="(grade, index) in sentSelectedPackage.allDetails.sent_received_detail"
                  v-bind:key="index"
                >
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

          <div id="damaged" class="col s12">
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
                <tr
                  v-for="(grade, index) in sentSelectedPackage.allDetails.receive_dmg_grades_detail"
                  v-bind:key="index"
                >
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
          <div id="lost" class="col s12">
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
                <tr
                  v-for="(grade, index) in sentSelectedPackage.allDetails.receive_lost_grades_detail"
                  v-bind:key="index"
                >
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
        </div>
      </div>
      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small">{{$t(`text.close`)}}</a>
      </div>
    </div>

    <!-- Received Modal Structure -->
    <div id="modalRecvdShipmentDetails" class="modal modal-fixed-footer">
      <div class="modal-content">
        <h4>{{$t(`text.shipment_details`)}}</h4>
        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_title`)}}</td>
              <td>{{recvdSelectedPackage.package.title}}</td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_received`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.total_general}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.shipment_description`)}}</td>
              <td>{{recvdSelectedPackage.package.description}}</td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_safe`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.total_safe}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.sent_date`)}}</td>
              <td>
                {{recvdSelectedPackage.package.send_date == null ? "" :
                moment(recvdSelectedPackage.package.send_date, 'YYYY-M-D').format('jYYYY-jM-jD')
                }} ({{recvdSelectedPackage.package.send_date}})
              </td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_damaged`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.damaged}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.received_date`)}}</td>
              <td>
                {{recvdSelectedPackage.allDetails.sent_shipment.receive_date == null ? "" :
                moment(recvdSelectedPackage.allDetails.sent_shipment.receive_date, 'YYYY-M-D').format('jYYYY-jM-jD')
                }} ({{recvdSelectedPackage.allDetails.sent_shipment.receive_date}})
              </td>

              <td
                style="font-weight: bold;"
                class="red-text text-lighten-1"
              >{{$t(`text.total_lost`)}}</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.lost}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.from`)}}</td>
              <td>{{recvdSelectedPackage.package.From_title}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.to`)}}</td>
              <td>{{recvdSelectedPackage.package.To_title}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.sender_name`)}}</td>
              <td>{{recvdSelectedPackage.package.sender_name}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">{{$t(`text.current_status`)}}</td>
              <td v-if="recvdSelectedPackage.package.recieved == 1">
                <div class="receivedStatus">{{$t(`text.received`)}}</div>
              </td>

              <td v-if="recvdSelectedPackage.package.recieved == 0">
                <div class="pendingStatus">{{$t(`text.pending`)}}</div>
                <a
                  class="waves-effect modal-close light-blue darken-4 btn-small"
                  style="margin-top:  5px; text-transform: none;"
                  @click="loadReceivePackagePage"
                >
                  <i class="material-icons left">list</i>
                  {{$t(`text.receive`)}}
                </a>
              </td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <div style="margin-top: 10px; margin-bottom: 20px;">
          <table class="striped">
            <thead>
              <tr
                style="color: #ee6e73; font-size: 14px !important; text-transform: uppercase; font-weight: normal !important;"
              >
                <td>{{$t(`text.document_title`)}}</td>
                <td>{{$t(`text.type`)}}</td>
                <td>{{$t(`text.download`)}}</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, index) in selectedPackageDocs.sent_docs" v-bind:key="index">
                <td>{{doc.title}}</td>
                <td>{{$t(`text.sent`)}}</td>
                <td>
                  <a
                    class="btn btn-small"
                    @click="downloadShipmentDocument(doc.id)"
                  >{{$t(`text.download`)}}</a>
                </td>
              </tr>

              <tr v-for="(doc, index) in selectedPackageDocs.reciept_docs" v-bind:key="index+4520">
                <td>{{doc.title}}</td>
                <td>{{$t(`text.received`)}}</td>
                <td>
                  <a
                    class="btn btn-small"
                    @click="downloadShipmentDocument(doc.id)"
                  >{{$t(`text.download`)}}</a>
                </td>
              </tr>

              <tr
                v-if="selectedPackageDocs.reciept_docs.length < 1 && selectedPackageDocs.sent_docs.length < 1"
              >
                <td>{{$t(`text.no_uploaded_documents`)}}</td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <a
            class="modal-close waves-effect btn-small light-blue darken-4"
            @click="showUploadDialog"
          >{{$t(`text.upload_new`)}}</a>
        </div>

        <div class="row" style="margin-top: 10px;">
          <div class="col s12">
            <ul class="tabs">
              <li class="tab col s3">
                <a class="active" href="#r_sent">{{$t(`text.sent`)}}</a>
              </li>
              <li class="tab col s3">
                <a href="#r_received">{{$t(`text.received`)}}</a>
              </li>
              <li class="tab col s3">
                <a href="#r_damaged">{{$t(`text.damaged`)}}</a>
              </li>
              <li class="tab col s3">
                <a href="#r_lost">{{$t(`text.lost`)}}</a>
              </li>
            </ul>
          </div>
          <div id="r_sent" class="col s12">
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
                <tr
                  v-for="(grade, index) in recvdSelectedPackage.allDetails.sent_detail"
                  v-bind:key="index"
                >
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

          <div id="r_received" class="col s12">
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
                <tr
                  v-for="(grade, index) in recvdSelectedPackage.allDetails.sent_received_detail"
                  v-bind:key="index"
                >
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

          <div id="r_damaged" class="col s12">
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
                <tr
                  v-for="(grade, index) in recvdSelectedPackage.allDetails.receive_dmg_grades_detail"
                  v-bind:key="index"
                >
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
          <div id="r_lost" class="col s12">
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
                <tr
                  v-for="(grade, index) in recvdSelectedPackage.allDetails.receive_lost_grades_detail"
                  v-bind:key="index"
                >
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
        </div>
      </div>
      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small">{{$t(`text.close`)}}</a>
      </div>
    </div>

    <!-- Upload Document Modal Structure -->
    <div id="modalUploadDocument" class="modal">
      <div class="modal-content">
        <h4>{{$t(`text.upload_document`)}}</h4>

        <div v-if="!showUploadProgress" style="margin-top: 20px;">
          <div class="row">
            <div class="form-field">
              <label for="documentTitle">{{$t(`text.document_title`)}}</label>
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
                <span>{{$t(`text.select_file`)}}</span>
                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()" required />
              </div>
              <div class="file-path-wrapper">
                <input class="file-path validate" type="text" />
              </div>
            </div>
          </div>
        </div>

        <div v-if="showUploadProgress" style="margin-top: 20px;">
          <p>{{$t(`text.uploading_document_wait`)}}</p>
          <div class="progress">
            <div class="indeterminate"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button
          class="waves-effect light-blue darken-4 btn-small"
          @click="uploadShipmentDocument"
        >{{$t(`text.upload_document`)}}</button>
        <a class="modal-close waves-effect btn-small">{{$t(`text.close`)}}</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",

  mounted: function() {
    console.log(this.$store.getters.userObj);

    // this.$Progress.start();

    this.getUserNode();

    this.getThirdLanguages();

    this.getSentPackages();
    this.getReceivedPackages();
  },

  data() {
    return {
      uploadDoc: {
        type: "",
        title: "",
        file: ""
      },

      showUploadProgress: false,

      sentPackages: {},
      receivedPackages: {},

      selectedPackageDocs: { sent_docs: [], reciept_docs: [] },

      sentSelectedPackage: {
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      },

      recvdSelectedPackage: {
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      }
    };
  },

  methods: {
    handleFileUpload() {
      this.uploadDoc.file = this.$refs.file.files[0];
    },

    uploadShipmentDocument: function() {
      if (this.uploadDoc.title != "") {
        if (this.uploadDoc.type == "sent") {
          this.uploadShipmentSentDocument();
        } else if (this.uploadDoc.type == "received") {
          this.uploadShipmentReceivedDocument();
        }
      }
    },

    uploadShipmentSentDocument: function() {
      var instance = this;
      this.showUploadProgress = true;

      let sent_doc = new FormData();
      sent_doc.append("sent_doc[file][0]", this.uploadDoc.file);
      sent_doc.append(
        "sent_doc[shipment_id]",
        this.sentSelectedPackage.package.id
      );
      sent_doc.append("sent_doc[title]", this.uploadDoc.title);

      this.$http
        .post("/shipments/documents/sent/store", sent_doc, {
          params: this.$http.defaults.params,
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
          instance.showUploadProgress = false;
          instance.hideUploadDialog();
        })
        .catch(function(error) {
          console.log(error.response);
          instance.showUploadProgress = false;
          alert(this.$t(`text.upload_document_error`));
        });
    },

    uploadShipmentReceivedDocument: function() {
      var instance = this;
      this.showUploadProgress = true;

      let recieve_doc = new FormData();
      recieve_doc.append("recieve_doc[file][0]", this.uploadDoc.file);
      recieve_doc.append(
        "recieve_doc[shipment_id]",
        this.recvdSelectedPackage.allDetails.sent_shipment.id
      );
      recieve_doc.append("recieve_doc[title]", this.uploadDoc.title);

      this.$http
        .post("/shipments/documents/receive/store", recieve_doc, {
          params: this.$http.defaults.params,
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
          instance.showUploadProgress = false;
          instance.hideUploadDialog();
        })
        .catch(function(error) {
          console.log(error.response);
          instance.showUploadProgress = false;
          alert(this.$t(`text.upload_document_error`));
        });
    },

    downloadShipmentDocument: function(fileId) {
      var instance = this;

      this.$http
        .get("/shipments/documents/" + fileId, {
          responseType: "blob",
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.forceFileDownload(response, fileId);
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    forceFileDownload: function(response, fileId) {
      const url = window.URL.createObjectURL(response.data);
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "document_" + fileId);
      link.click();
    },

    getShipmentDocuments: function(shipmentId) {
      var instance = this;

      this.$http
        .get("/shipments/" + shipmentId + "/documents", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          instance.selectedPackageDocs = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    showUploadDialog: function() {
      this.uploadDoc.title = "";
      this.uploadDoc.file = "";

      this.showUploadProgress = false;

      var modalUpload = document.querySelector("#modalUploadDocument");
      var instance = M.Modal.getInstance(modalUpload);
      instance.open();
    },

    hideUploadDialog: function() {
      var modalUpload = document.querySelector("#modalUploadDocument");
      var instance = M.Modal.getInstance(modalUpload);
      instance.close();
    },

    getThirdLanguages: function() {
      var instance = this;

      this.$http
        .get("/meta/languages/third", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          // console.log(response.data);
          instance.$store.commit("setThirdLanguagesObj", response.data);
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    loadMoreReceivedPackages: function() {
      var nextUrl = this.receivedPackages.next_page_url;
      nextUrl = nextUrl.substring(1, nextUrl.length);

      var instance = this;

      if (nextUrl != null) {
        this.$http
          .get("/shipments/received" + nextUrl, {
            params: this.$http.defaults.params
          })
          .then(function(response) {
            console.log(response.data);

            let prevRecords = instance.receivedPackages.data;
            let newRecords = response.data.data;

            instance.receivedPackages = response.data;

            instance.pushNewReceivedPackages(prevRecords, newRecords);
          })
          .catch(function(error) {
            console.log(error);
          });
      }
    },

    pushNewReceivedPackages: function(prevRecords, newRecords) {
      this.receivedPackages.data = prevRecords;

      for (var key in newRecords) {
        this.receivedPackages.data.push(newRecords[key]);
      }
    },

    loadMoreSentPackages: function() {
      var nextUrl = this.sentPackages.next_page_url;
      nextUrl = nextUrl.substring(1, nextUrl.length);

      var instance = this;

      if (nextUrl != null) {
        this.$http
          .get("/shipments/sent" + nextUrl, {
            params: instance.$http.defaults.params
          })
          .then(function(response) {
            console.log(response.data);

            let prevRecords = instance.sentPackages.data;
            let newRecords = response.data.data;

            instance.sentPackages = response.data;

            instance.pushNewSentPackages(prevRecords, newRecords);
          })
          .catch(function(error) {
            console.log(error);
          });
      }
    },

    pushNewSentPackages: function(prevRecords, newRecords) {
      this.sentPackages.data = prevRecords;

      for (var key in newRecords) {
        this.sentPackages.data.push(newRecords[key]);
      }
    },

    loadReceivePackagePage: function() {
      this.$store.commit("setReceivingPackage", this.recvdSelectedPackage);
      this.$router.push("/receive-package");
    },

    viewSentPackageDetails: function(pckg) {
      this.sentSelectedPackage.package = pckg;
      this.uploadDoc.type = "sent";
      this.selectedPackageDocs = { sent_docs: [], reciept_docs: [] };

      this.getSentPackageAllDetails(pckg.id);
      this.getShipmentDocuments(pckg.id);
    },

    getSentPackageAllDetails: function(id) {
      var instance = this;

      this.$http
        .get("/shipments/sent/" + id + "/all", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.sentSelectedPackage.allDetails = response.data;
          console.log(instance.sentSelectedPackage);
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    viewRecvdPackageDetails: function(pckg) {
      this.recvdSelectedPackage.package = pckg;
      this.uploadDoc.type = "received";
      this.selectedPackageDocs = { sent_docs: [], reciept_docs: [] };

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

          instance.getShipmentDocuments(
            instance.recvdSelectedPackage.allDetails.sent_shipment.id
          );
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getUserNode: function() {
      var instance = this;

      this.$http
        .get("/user/node", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.$store.commit("setNode", response.data);
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getSentPackages: function() {
      var instance = this;

      this.$http
        .get("/shipments/sent?page=1", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.sentPackages = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getReceivedPackages: function() {
      var instance = this;

      this.$http
        .get("/shipments/received?page=1", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.receivedPackages = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
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
</style>
