<template>
  <div>
    <div class="container" id="adminManagePackagesPage" style="width: 100%;">
      <div class="row">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Package Management</span>
              <p>You can use this section to manage all the packages related to a selected node on any level.</p>
              <br />
              <div class="form-field">
                <label for="ShipmentRecipientLevel">Node Level</label>
                <select
                  id="ShipmentRecipientLevel"
                  name="ShipmentRecipientLevel"
                  v-model="recipient.level"
                >
                  <option value disabled selected>Select Level</option>
                  <option
                    v-for="(level, index) in allLevels"
                    v-bind:key="index"
                    :value="level.id"
                  >{{level.title}}</option>
                </select>

                <select
                  id="ShipmentRecipientLevel2"
                  name="ShipmentRecipientLevel2"
                  v-model="recipient.province"
                >
                  <option value disabled selected>Select Province</option>
                  <option
                    v-for="(province, index) in allProvinces"
                    v-bind:key="index"
                    :value="province.id"
                  >{{province.en_name}}</option>
                </select>

                <select
                  id="ShipmentRecipientLevel3"
                  name="ShipmentRecipientLevel3"
                  v-model="recipient.district"
                >
                  <option value disabled selected>Select District</option>
                  <option
                    v-for="(district, index) in selectedProvinceDistricts"
                    v-bind:key="index"
                    :value="district.id"
                  >{{district.en_name}}</option>
                </select>
              </div>
              <br />

              <div class="form-field">
                <label for="ShipmentRecipient">Node</label>
                <select id="ShipmentRecipient" name="ShipmentRecipient" v-model="recipient.node">
                  <option value disabled selected>Select Node</option>
                  <option
                    v-for="(node, index) in selectedLevelChildNodes"
                    v-bind:key="index"
                    :value="node.id"
                  >{{node.title}}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="editMode">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Edit Shipment</span>

              <button
                v-if="editSelectedPackage.type == 'sent'"
                class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                href="#modalSentShipmentDetails"
                @click="viewSentPackageDetails(editSelectedPackage.package)"
              >View Details</button>

              <button
                v-if="editSelectedPackage.type == 'received'"
                class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                href="#modalRecvdShipmentDetails"
                @click="viewRecvdPackageDetails(editSelectedPackage.package)"
              >View Details</button>

              <button @click="exitEditMode" class="waves-effect btn btn-small">Exit Edit Mode</button>

              <table class="striped" style="margin-top: 10px;">
                <tbody>
                  <tr>
                    <td style="font-weight: bold;">Shipment Title</td>
                    <td>{{editSelectedPackage.package.title}}</td>

                    <td style="font-weight: bold;">From</td>
                    <td>
                      {{editSelectedPackage.package.From_title}}
                      {{editSelectedPackage.package.from_node}}
                    </td>
                  </tr>

                  <tr>
                    <td style="font-weight: bold;">Shipment Description</td>
                    <td>{{editSelectedPackage.package.description}}</td>

                    <td style="font-weight: bold;">To</td>
                    <td>
                      {{editSelectedPackage.package.To_title}}
                      {{editSelectedPackage.package.to_node}}
                    </td>
                  </tr>

                  <tr>
                    <td style="font-weight: bold;">Sent Date</td>
                    <td>{{editSelectedPackage.package.send_date}}</td>

                    <td style="font-weight: bold;">Current Status</td>
                    <td
                      v-if="editSelectedPackage.package.recieved == 1 || editSelectedPackage.package.recieved_status == 1"
                    >
                      <div class="receivedStatus">Received</div>
                    </td>

                    <td
                      v-if="editSelectedPackage.package.recieved == 0 || editSelectedPackage.package.recieved_status == 0"
                    >
                      <div class="pendingStatus">Pending</div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <div style="margin-top: 20px;">
                <button
                  class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                  href="#modalDeleteShipment"
                >Delete Shipment</button>
                <button
                  class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                  href="#modalClearReceiveRecords"
                >Clear Receive Records</button>

                <button
                  class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                  href="#modalEditGeneralInfo"
                  @click="editGeneralInfo"
                >Edit General Information</button>

                <button
                  href="#modalChangeRecipient"
                  class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                >Change Recipient</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="recipient.node > 0 && !editMode">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Current Inventory</span>

              <table class="striped">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Grade</th>
                    <th>Language</th>
                    <th>Quantity</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="(title, index) in currBalance" v-bind:key="index">
                    <td>{{title.title}}</td>
                    <td>{{title.grade}}</td>
                    <td>{{title.lang}}</td>
                    <td>{{title.total}}</td>
                    <td>
                      <button
                        @click="deleteBalanceTitle(title.id)"
                        class="waves-effect light-blue darken-4 btn btn-small"
                      >Delete</button>
                      <button
                        @click="editBalanceTitle(title)"
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalEditBalanceTitle"
                      >Edit</button>
                    </td>
                  </tr>

                  <tr v-show="currBalance.length < 1">
                    <td>No data available or inventory records empty.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
              <br />

              <button class="waves-effect light-blue darken-4 btn btn-small">Add New</button>
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="recipient.node > 0 && !editMode">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Sent Packages</span>

              <table class="striped">
                <thead>
                  <tr>
                    <th>Package ID</th>
                    <th>Title</th>
                    <th>Sent Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="pckg in sentPackages" v-bind:key="pckg.id">
                    <td>{{pckg.id}}</td>
                    <td>{{pckg.title}}</td>
                    <td>{{pckg.send_date}}</td>
                    <td>{{pckg.from_node}}</td>
                    <td>{{pckg.to_node}}</td>
                    <td v-if="pckg.recieved_status == 1">
                      <div class="receivedStatus">Received</div>
                    </td>

                    <td v-if="pckg.recieved_status == 0">
                      <div class="pendingStatus">Pending</div>
                    </td>
                    <td>
                      <button
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalSentShipmentDetails"
                        @click="viewSentPackageDetails(pckg)"
                      >Details</button>

                      <button
                        @click="editPackage(pckg, 'sent')"
                        class="waves-effect light-blue darken-4 btn btn-small"
                      >Edit</button>
                    </td>
                  </tr>

                  <tr v-show="!sentPackages || !sentPackages.length > 0">
                    <td>No package sent yet.</td>
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
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="recipient.node > 0 && !editMode">
        <div class="col s12">
          <div class="card left-align" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Received Packages</span>

              <table class="striped">
                <thead>
                  <tr>
                    <th>Package ID</th>
                    <th>Title</th>
                    <th>Sent Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="pckg in receivedPackages" v-bind:key="pckg.id">
                    <td>{{pckg.recieved_id}}</td>
                    <td>{{pckg.title}}</td>
                    <td>{{pckg.send_date}}</td>
                    <td>{{pckg.From_title}}</td>
                    <td>{{pckg.To_title}}</td>
                    <td v-if="pckg.recieved == 1">
                      <div class="receivedStatus">Received</div>
                    </td>

                    <td v-if="pckg.recieved == 0">
                      <div class="pendingStatus">Pending</div>
                    </td>

                    <td>
                      <button
                        @click="viewRecvdPackageDetails(pckg)"
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalRecvdShipmentDetails"
                      >Details</button>

                      <button
                        @click="editPackage(pckg, 'received')"
                        class="waves-effect light-blue darken-4 btn btn-small"
                      >Edit</button>
                    </td>
                  </tr>

                  <tr v-show="!receivedPackages || !receivedPackages.length > 0">
                    <td>No package received yet.</td>
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
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-if="recipient.node > 0 && !editMode">
        <div class="col s11 offset-s1">
          <div class="card left-align hoverable" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Delivered to Students</span>

              <table class="striped">
                <thead>
                  <tr>
                    <th>Package ID</th>
                    <th>Title</th>
                    <th>Sent Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="pckg in toBeneficiaryPackages" v-bind:key="pckg.id">
                    <td>{{pckg.recieved_id}}</td>
                    <td>{{pckg.title}}</td>
                    <td>{{pckg.send_date}}</td>
                    <td>{{pckg.From_title}}</td>
                    <td>Beneficiaries</td>
                    <td v-if="pckg.recieved == 1">
                      <div class="receivedStatus">Received</div>
                    </td>

                    <td v-if="pckg.recieved == 0">
                      <div class="pendingStatus">Pending</div>
                    </td>

                    <td>
                      <button
                        @click="viewRecvdPackageDetails(pckg)"
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalRecvdShipmentDetails"
                      >Details</button>

                      <button
                        @click="editPackage(pckg, 'received')"
                        class="waves-effect light-blue darken-4 btn btn-small"
                      >Edit</button>
                    </td>
                  </tr>

                  <tr v-show="!toBeneficiaryPackages || !toBeneficiaryPackages.length > 0">
                    <td>No package delivered yet.</td>
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
        </div>
      </div>

      <div class="row" v-if="recipient.node > 0 && !editMode">
        <div class="col s11 offset-s1">
          <div class="card left-align hoverable" style="width: 100%;">
            <div class="card-content">
              <span class="card-title">Received from Students</span>

              <table class="striped">
                <thead>
                  <tr>
                    <th>Package ID</th>
                    <th>Title</th>
                    <th>Sent Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="pckg in fromBeneficiaryPackages" v-bind:key="pckg.id">
                    <td>{{pckg.recieved_id}}</td>
                    <td>{{pckg.title}}</td>
                    <td>{{pckg.send_date}}</td>
                    <td>Beneficiaries</td>
                    <td>{{pckg.To_title}}</td>
                    <td v-if="pckg.recieved == 1">
                      <div class="receivedStatus">Received</div>
                    </td>

                    <td v-if="pckg.recieved == 0">
                      <div class="pendingStatus">Pending</div>
                    </td>

                    <td>
                      <button
                        @click="viewRecvdPackageDetails(pckg)"
                        class="waves-effect light-blue darken-4 btn btn-small modal-trigger"
                        href="#modalRecvdShipmentDetails"
                      >Details</button>

                      <button
                        @click="editPackage(pckg, 'received')"
                        class="waves-effect light-blue darken-4 btn btn-small"
                      >Edit</button>
                    </td>
                  </tr>

                  <tr v-show="!fromBeneficiaryPackages || !fromBeneficiaryPackages.length > 0">
                    <td>No package received yet.</td>
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
        </div>
      </div>
    </div>

    <!-- modalChangeRecipient Modal Structure -->
    <div id="modalChangeRecipient" class="modal">
      <div class="modal-content">
        <h4>Change Recipient</h4>

        <div
          class="row"
          v-if="editSelectedPackage.package.recieved == 0 || editSelectedPackage.package.recieved_status == 0"
        >
          <div class="form-field">
            <label for="editShipmentRecipientLevel">Shipment Recipient Level</label>
            <select
              id="editShipmentRecipientLevel"
              name="editShipmentRecipientLevel"
              v-model="changeRecipient.level"
            >
              <option value disabled selected>Select Level</option>
              <option
                v-for="(level, index) in allLevels"
                v-bind:key="index"
                :value="level.id"
              >{{level.title}}</option>
            </select>

            <select
              id="editShipmentRecipientLevel2"
              name="editShipmentRecipientLevel2"
              v-model="changeRecipient.province"
            >
              <option value disabled selected>Select Province</option>
              <option
                v-for="(province, index) in allProvinces"
                v-bind:key="index"
                :value="province.id"
              >{{province.en_name}}</option>
            </select>

            <select
              id="editShipmentRecipientLevel3"
              name="editShipmentRecipientLevel3"
              v-model="changeRecipient.district"
            >
              <option value disabled selected>Select District</option>
              <option
                v-for="(district, index) in editSelectedProvinceDistricts"
                v-bind:key="index"
                :value="district.id"
              >{{district.en_name}}</option>
            </select>
          </div>
          <br />

          <div class="form-field">
            <label for="editShipmentRecipient">Shipment Recipient</label>
            <select
              id="editShipmentRecipient"
              name="editShipmentRecipient"
              v-model="changeRecipient.node"
            >
              <option value disabled selected>Select Recipient</option>
              <option
                v-for="(node, index) in editSelectedLevelChildNodes"
                v-bind:key="index"
                :value="node.id"
              >{{node.title}}</option>
            </select>
          </div>
        </div>

        <div v-else>
          <p>You cannot change the recipient of an already received package. The package status has to be "Pending" to change the recipient.</p>
        </div>
      </div>

      <div class="modal-footer">
        <button
          v-if="editSelectedPackage.package.recieved == 0 || editSelectedPackage.package.recieved_status == 0"
          class="modal-close waves-effect btn-small"
          @click="updateRecipient"
        >Update</button>
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- modalEditGeneralInfo Modal Structure -->
    <div id="modalEditGeneralInfo" class="modal">
      <div class="modal-content">
        <h4>Edit General Information</h4>

        <div class="row">
          <div class="form-field">
            <label for="ShipmentTitle">Shipment Title</label>
            <input
              type="text"
              id="ShipmentTitle"
              name="ShipmentTitle"
              placeholder="Shipment Title"
              class="validate"
              required
              aria-required="true"
              v-model="newGeneralInfo.title"
            />
          </div>

          <br />
          <div class="form-field">
            <label for="ShipmentDescription">Shipment Description</label>
            <input
              type="text"
              id="ShipmentDescription"
              name="ShipmentDescription"
              placeholder="Shipment Description"
              v-model="newGeneralInfo.description"
            />
          </div>

          <br />
          <div class="form-field">
            <label for="projectDDL">Textbooks Project</label>
            <select id="projectDDL" name="projectDDL" v-model="newGeneralInfo.project">
              <option value disabled selected>Select Project</option>
              <option
                v-for="(project, index) in allProjects"
                v-bind:key="index"
                :value="project.id"
              >{{project.title}}</option>
            </select>
          </div>

          <br />

          <div class="form-field">
            <label for="SendDate">Send Date</label>
            <input
              type="date"
              id="SendDate"
              name="SendDate"
              v-model="newGeneralInfo.sendDate"
              class="validate"
              required
              aria-required="true"
            />
          </div>

          <br />

          <div class="form-field">
            <label for="ReceiveDate">Receive Date</label>
            <input
              type="date"
              id="ReceiveDate"
              name="ReceiveDate"
              v-model="newGeneralInfo.receiveDate"
              class="validate"
              required
              aria-required="true"
            />
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="modal-close waves-effect btn-small" @click="updateGeneralInfo">Update</button>
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- modalEditBalanceTitle Modal Structure -->
    <div id="modalEditBalanceTitle" class="modal">
      <div class="modal-content">
        <h4>Update Title Quantity</h4>

        <div class="row">
          <div class="form-field">
            <label for="titleQuantity">Quantity</label>
            <input
              type="number"
              id="titleQuantity"
              name="titleQuantity"
              placeholder="Quantity"
              class="validate"
              required
              aria-required="true"
              v-model="balanceRecordObj.newTotal"
            />
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="modal-close waves-effect btn-small" @click="updateBalanceTitle">Update</button>
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- modalDeleteShipment Modal Structure -->
    <div id="modalDeleteShipment" class="modal">
      <div class="modal-content">
        <h4>Delete Shipment</h4>

        <p>This operation will completely delete the selected shipment along with all the associated records.</p>

        <button
          class="waves-effect light-blue darken-4 btn-small"
          @click="deleteShipment"
        >Delete Shipment</button>
        <br />
        <br />
        <div class="row">
          <form class="col s12">
            <div class="row">
              <textarea id="outputTA" style="height: 200px; width: 100%;" v-model="adminOutput">..</textarea>
            </div>
          </form>
        </div>
      </div>

      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- modalClearReceiveRecords Modal Structure -->
    <div id="modalClearReceiveRecords" class="modal">
      <div class="modal-content">
        <h4>Clear Receive Records</h4>

        <p>This operation will clear all the receive records including fine, lost, and damaged records of the selected shipment.</p>

        <button
          class="waves-effect light-blue darken-4 btn-small"
          @click="clearReceiveRecords"
        >Clear Receive Records</button>
        <br />
        <br />
        <div class="row">
          <form class="col s12">
            <div class="row">
              <textarea id="outputTA" style="height: 200px; width: 100%;" v-model="adminOutput">..</textarea>
            </div>
          </form>
        </div>
      </div>

      <div class="modal-footer">
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- Sent Modal Structure -->
    <div id="modalSentShipmentDetails" class="modal modal-fixed-footer">
      <div class="modal-content">
        <h4>Shipment Details</h4>
        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">Shipment Title</td>
              <td>{{sentSelectedPackage.package.title}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Received</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.total_general}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Shipment Description</td>
              <td>{{sentSelectedPackage.package.description}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Safe</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.total_safe}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Sent Date</td>
              <td>{{sentSelectedPackage.package.send_date}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Damaged</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.damaged}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Received Date</td>
              <td>{{sentSelectedPackage.package.receive_date}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Lost</td>
              <td
                class="red-text text-lighten-1"
              >{{sentSelectedPackage.allDetails.receive_record.lost}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">From</td>
              <td>{{sentSelectedPackage.package.from_node}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">To</td>
              <td>{{sentSelectedPackage.package.to_node}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Sender Name</td>
              <td>{{sentSelectedPackage.package.sender_name}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Current Status</td>
              <td v-if="sentSelectedPackage.package.recieved_status == 1">
                <div class="receivedStatus">Received</div>
              </td>

              <td v-if="sentSelectedPackage.package.recieved_status == 0">
                <div class="pendingStatus">Pending</div>
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
                <td>Document Title</td>
                <td>Type</td>
                <td>Download</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, index) in selectedPackageDocs.sent_docs" v-bind:key="index">
                <td>{{doc.title}}</td>
                <td>Sent</td>
                <td style="text-align: right;">
                  <a class="btn btn-small" @click="downloadShipmentDocument(doc.id)">Download</a>
                </td>
              </tr>

              <tr v-for="(doc, index) in selectedPackageDocs.reciept_docs" v-bind:key="index+4520">
                <td>{{doc.title}}</td>
                <td>Received</td>
                <td style="text-align: right;">
                  <a class="btn btn-small" @click="downloadShipmentDocument(doc.id)">Download</a>
                </td>
              </tr>

              <tr
                v-if="selectedPackageDocs.reciept_docs.length < 1 && selectedPackageDocs.sent_docs.length < 1"
              >
                <td>No documents uploaded yet.</td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <a
            class="modal-close waves-effect btn-small light-blue darken-4"
            @click="showUploadDialog"
          >Upload New</a>
        </div>

        <div class="row" style="margin-top: 10px;">
          <div class="col s12">
            <ul class="tabs">
              <li class="tab col s3">
                <a class="active" href="#sent">Sent</a>
              </li>
              <li class="tab col s3">
                <a href="#received">Received</a>
              </li>
              <li class="tab col s3">
                <a href="#damaged">Damaged</a>
              </li>
              <li class="tab col s3">
                <a href="#lost">Lost</a>
              </li>
            </ul>
          </div>
          <div id="sent" class="col s12">
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- Received Modal Structure -->
    <div id="modalRecvdShipmentDetails" class="modal modal-fixed-footer">
      <div class="modal-content">
        <h4>Shipment Details</h4>
        <table class="striped">
          <tbody>
            <tr>
              <td style="font-weight: bold;">Shipment Title</td>
              <td>{{recvdSelectedPackage.package.title}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Received</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.total_general}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Shipment Description</td>
              <td>{{recvdSelectedPackage.package.description}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Safe</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.total_safe}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Sent Date</td>
              <td>{{recvdSelectedPackage.package.send_date}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Damaged</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.damaged}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Received Date</td>
              <td>{{recvdSelectedPackage.allDetails.sent_shipment.receive_date}}</td>

              <td style="font-weight: bold;" class="red-text text-lighten-1">Total Lost</td>
              <td
                class="red-text text-lighten-1"
              >{{recvdSelectedPackage.allDetails.receive_record.lost}}</td>
            </tr>

            <tr>
              <td style="font-weight: bold;">From</td>
              <td>{{recvdSelectedPackage.package.From_title}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">To</td>
              <td>{{recvdSelectedPackage.package.To_title}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Sender Name</td>
              <td>{{recvdSelectedPackage.package.sender_name}}</td>
              <td></td>
              <td></td>
            </tr>

            <tr>
              <td style="font-weight: bold;">Current Status</td>
              <td v-if="recvdSelectedPackage.package.recieved == 1">
                <div class="receivedStatus">Received</div>
              </td>

              <td v-if="recvdSelectedPackage.package.recieved == 0">
                <div class="pendingStatus">Pending</div>
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
                <td>Document Title</td>
                <td>Type</td>
                <td>Download</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, index) in selectedPackageDocs.sent_docs" v-bind:key="index">
                <td>{{doc.title}}</td>
                <td>Sent</td>
                <td style="text-align: right;">
                  <a class="btn btn-small" @click="downloadShipmentDocument(doc.id)">Download</a>
                </td>
              </tr>

              <tr v-for="(doc, index) in selectedPackageDocs.reciept_docs" v-bind:key="index+4520">
                <td>{{doc.title}}</td>
                <td>Received</td>
                <td style="text-align: right;">
                  <a class="btn btn-small" @click="downloadShipmentDocument(doc.id)">Download</a>
                </td>
              </tr>

              <tr
                v-if="selectedPackageDocs.reciept_docs.length < 1 && selectedPackageDocs.sent_docs.length < 1"
              >
                <td>No documents uploaded yet.</td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <a
            class="modal-close waves-effect btn-small light-blue darken-4"
            @click="showUploadDialog"
          >Upload New</a>
        </div>

        <div class="row" style="margin-top: 10px;">
          <div class="col s12">
            <ul class="tabs">
              <li class="tab col s3">
                <a class="active" href="#r_sent">Sent</a>
              </li>
              <li class="tab col s3">
                <a href="#r_received">Received</a>
              </li>
              <li class="tab col s3">
                <a href="#r_damaged">Damaged</a>
              </li>
              <li class="tab col s3">
                <a href="#r_lost">Lost</a>
              </li>
            </ul>
          </div>
          <div id="r_sent" class="col s12">
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
            <table>
              <thead>
                <tr>
                  <th>Grade</th>
                  <th>Title</th>
                  <th>Language</th>
                  <th>Quantity</th>
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
        <a class="modal-close waves-effect light-blue darken-4 btn-small">Close</a>
      </div>
    </div>

    <!-- Upload Document Modal Structure -->
    <div id="modalUploadDocument" class="modal">
      <div class="modal-content">
        <h4>Upload Document</h4>

        <div v-if="!showUploadProgress" style="margin-top: 20px;">
          <div class="row">
            <div class="form-field">
              <label for="documentTitle">Document Title</label>
              <input
                type="text"
                id="documentTitle"
                name="documentTitle"
                placeholder="Document Title"
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
                <span>Select File</span>
                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()" required />
              </div>
              <div class="file-path-wrapper">
                <input class="file-path validate" type="text" />
              </div>
            </div>
          </div>
        </div>

        <div v-if="showUploadProgress" style="margin-top: 20px;">
          <p>Uploading document. Please wait...</p>
          <div class="progress">
            <div class="indeterminate"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button
          class="waves-effect light-blue darken-4 btn-small"
          @click="uploadShipmentDocument"
        >Upload Document</button>
        <a class="modal-close waves-effect btn-small">Close</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminManagePackages",

  mounted: function() {
    // console.log(this.$store.getters.userObj);
    // console.log(this.$store.getters.userNodeObj);
  },

  created() {
    this.getLevels();
    this.getProvinces();
    this.getProjects();
  },

  updated() {
    var elems = document.querySelectorAll("select");
    var instances = M.FormSelect.init(elems);
  },

  watch: {
    // change recipient dialog
    // execute on level change
    "changeRecipient.level": function() {
      this.changeRecipient.province = 0;
    },

    // get districts of the selected province based on province change
    "changeRecipient.province": function(selectedProvince, previousProvince) {
      this.changeRecipient.district = 0;
      this.getEditDistricts();
    },

    // get child nodes of selected level / province / district
    "changeRecipient.district": function(selectedDistrict, previousDistrict) {
      this.changeRecipient.node = 0;
      this.getEditLevelChildNodes();
    },
    // change recipient dialog

    // execute on level change
    "recipient.level": function() {
      this.recipient.province = 0;
    },

    // get districts of the selected province based on province change
    "recipient.province": function(selectedProvince, previousProvince) {
      this.recipient.district = 0;
      this.getDistricts();
    },

    // get child nodes of selected level / province / district
    "recipient.district": function(selectedDistrict, previousDistrict) {
      this.recipient.node = 0;
      this.getLevelChildNodes();
    },

    "recipient.node": function() {
      this.exitEditMode();

      this.getCurrentBalance();
      this.getReceivedPackages();
      this.getSentPackages();
      this.getToBeneficiaryPackages();
      this.getFromBeneficiaryPackages();
    }
  },

  data() {
    return {
      changeRecipient: {
        level: 0,
        province: 0,
        district: 0,
        node: 0
      },

      currBalance: [],
      balanceRecordObj: {},

      editMode: false,

      recipient: {
        level: 0,
        province: 0,
        district: 0,
        node: 0
      },

      allLevels: [],
      allProvinces: [],
      allProjects: [],
      selectedProvinceDistricts: [],
      selectedLevelChildNodes: [],

      editSelectedProvinceDistricts: [],
      editSelectedLevelChildNodes: [],

      sentPackages: {},
      receivedPackages: {},
      toBeneficiaryPackages: [],
      fromBeneficiaryPackages: [],

      uploadDoc: {
        type: "",
        title: "",
        file: ""
      },
      selectedPackageDocs: { sent_docs: [], reciept_docs: [] },
      showUploadProgress: false,

      newGeneralInfo: {
        title: "",
        description: "",
        sendDate: "",
        receiveDate: "",
        project: 0
      },

      editSelectedPackage: {
        type: "",
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      },
      adminOutput: "",

      recvdSelectedPackage: {
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      },

      sentSelectedPackage: {
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      }
    };
  },

  methods: {
    updateRecipient: function() {
      var instance = this;

      var recObj = this.changeRecipient;
      recObj.id = this.editSelectedPackage.allDetails.sent_shipment.id;

      this.$http
        .post("/admin/shipments/recipient/update", recObj, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
        })
        .catch(function(error) {
          console.log(error.response);
          alert("The recipient could not be changed. Please try again.");
        });
    },

    editGeneralInfo: function() {
      this.newGeneralInfo.title = this.editSelectedPackage.allDetails.sent_shipment.title;
      this.newGeneralInfo.description = this.editSelectedPackage.allDetails.sent_shipment.description;
      this.newGeneralInfo.sendDate = this.editSelectedPackage.allDetails.sent_shipment.send_date;
      this.newGeneralInfo.receiveDate = this.editSelectedPackage.allDetails.sent_shipment.receive_date;
      this.newGeneralInfo.project = this.editSelectedPackage.allDetails.sent_shipment.project_id;
    },

    updateGeneralInfo: function() {
      var instance = this;

      var infoObj = this.newGeneralInfo;
      infoObj.id = this.editSelectedPackage.allDetails.sent_shipment.id;

      this.$http
        .post("/admin/shipments/general/update", infoObj, {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          alert(response.data.message);
        })
        .catch(function(error) {
          console.log(error.response);
          alert("The information could not be updated. Please try again.");
        });
    },

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
          alert("The document could not be uploaded. Please try again.");
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
          alert("The document could not be uploaded. Please try again.");
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

    getLevels: function() {
      var instance = this;

      this.$http
        .get("/meta/levels", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.allLevels = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getLevelChildNodes: function() {
      var instance = this;

      if (
        this.recipient.level != 0 &&
        this.recipient.province != 0 &&
        this.recipient.district != 0
      ) {
        this.$http
          .get(
            "/meta/levels/" +
              this.recipient.level +
              "/" +
              this.recipient.province +
              "/" +
              this.recipient.district +
              "/nodes",
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            // console.log(response.data);

            instance.selectedLevelChildNodes = response.data;
          })
          .catch(function(error) {
            console.log(error);
          });
      }
    },

    getEditLevelChildNodes: function() {
      var instance = this;

      if (
        this.changeRecipient.level != 0 &&
        this.changeRecipient.province != 0 &&
        this.changeRecipient.district != 0
      ) {
        this.$http
          .get(
            "/meta/levels/" +
              this.changeRecipient.level +
              "/" +
              this.changeRecipient.province +
              "/" +
              this.changeRecipient.district +
              "/nodes",
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            // console.log(response.data);

            instance.editSelectedLevelChildNodes = response.data;
          })
          .catch(function(error) {
            console.log(error);
          });
      }
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

    getProvinces: function() {
      var instance = this;

      this.$http
        .get("/meta/provinces", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          // console.log(response.data);

          instance.allProvinces = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getDistricts: function() {
      var instance = this;

      this.$http
        .get("/meta/provinces/" + this.recipient.province + "/districts", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.selectedProvinceDistricts = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getEditDistricts: function() {
      var instance = this;

      this.$http
        .get(
          "/meta/provinces/" + this.changeRecipient.province + "/districts",
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          instance.editSelectedProvinceDistricts = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    deleteShipment: function() {
      var sId = this.editSelectedPackage.allDetails.sent_shipment.id;

      this.adminOutput +=
        "SID " + sId + ": completely deleting the selected shipment.\n";

      var instance = this;
      this.$http
        .get("/admin/shipments/" + sId + "/delete", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);
          instance.adminOutput += response.data.message;
        })
        .catch(function(error) {
          console.log(error);
          instance.adminOutput += error;
        });
    },

    clearReceiveRecords: function() {
      this.adminOutput +=
        "SRID " +
        this.editSelectedPackage.package.recieved_id +
        ": clearing receive records including fine, lost, and damaged records.\n";

      var instance = this;
      this.$http
        .get(
          "/admin/shipments/" +
            this.editSelectedPackage.package.recieved_id +
            "/clear",
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          instance.adminOutput += response.data.message;
        })
        .catch(function(error) {
          console.log(error);
          instance.adminOutput += error;
        });
    },

    exitEditMode: function() {
      this.editMode = false;

      this.editSelectedPackage = {
        package: {},
        allDetails: {
          sent_shipment: {},
          receive_record: {}
        }
      };

      this.getReceivedPackages();
      this.getSentPackages();
      this.getToBeneficiaryPackages();
      this.getFromBeneficiaryPackages();
    },

    editPackage: function(pckg, type) {
      this.editSelectedPackage.package = pckg;
      this.editSelectedPackage.type = type;
      this.selectedPackageDocs = { sent_docs: [], reciept_docs: [] };

      this.getEditPackageAllDetails(pckg.recieved_id);

      this.adminOutput = "";
      this.editMode = true;
    },

    getEditPackageAllDetails: function(id) {
      var instance = this;

      this.$http
        .get("/shipments/received/" + id + "/all", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          instance.editSelectedPackage.allDetails = response.data;
          console.log(instance.editSelectedPackage);

          instance.getShipmentDocuments(
            instance.editSelectedPackage.allDetails.sent_shipment.id
          );
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getToBeneficiaryPackages: function() {
      var instance = this;

      this.$http
        .get("/node/" + this.recipient.node + "/beneficiary/sent", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.toBeneficiaryPackages = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getFromBeneficiaryPackages: function() {
      var instance = this;

      this.$http
        .get("/node/" + this.recipient.node + "/beneficiary/received", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.fromBeneficiaryPackages = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    getCurrentBalance: function() {
      var instance = this;

      this.$http
        .get("/admin/node/" + this.recipient.node + "/balance", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.currBalance = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    deleteBalanceTitle: function(recordId) {
      console.log(recordId);

      var goAhead = confirm(
        "This will delete the title from the inventory, setting the balance to 0. Are you sure you want to proceed?"
      );

      if (goAhead) {
        var instance = this;

        this.$http
          .post(
            "/admin/balance/record/delete",
            {
              recordId: recordId
            },
            {
              params: this.$http.defaults.params
            }
          )
          .then(function(response) {
            console.log(response.data);
            instance.getCurrentBalance();
            alert(response.data.message);
          })
          .catch(function(error) {
            console.log(error.response);
          });
      }
    },

    editBalanceTitle: function(recordObj) {
      this.balanceRecordObj = recordObj;
      this.balanceRecordObj.newTotal = recordObj.total;
      console.log(this.balanceRecordObj);
    },

    updateBalanceTitle: function() {
      var instance = this;

      this.$http
        .post(
          "/admin/balance/record/update",
          {
            recordId: instance.balanceRecordObj.id,
            newTotal: instance.balanceRecordObj.newTotal
          },
          {
            params: this.$http.defaults.params
          }
        )
        .then(function(response) {
          console.log(response.data);
          instance.getCurrentBalance();
          alert(response.data.message);
        })
        .catch(function(error) {
          console.log(error.response);
        });
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

    getReceivedPackages: function() {
      var instance = this;

      this.$http
        .get("/shipments/" + this.recipient.node + "/received/all", {
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

    getSentPackages: function() {
      var instance = this;

      this.$http
        .get("/shipments/" + this.recipient.node + "/sent/all", {
          params: this.$http.defaults.params
        })
        .then(function(response) {
          console.log(response.data);

          instance.sentPackages = response.data;
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