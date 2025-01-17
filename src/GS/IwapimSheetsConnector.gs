function runUpdaterForChannels() {
  const salesUrl = "https://iwa.web.tr/sheets/amazonsales/";
  const fbaUrl = "https://iwa.web.tr/sheets/amazonfba/";
  const channels = {
    us: "Amazon.com",
    eu: "Amazon.eu",
    uk: "Amazon.co.uk",
    au: "Amazon.com.au",
    ca: "Amazon.ca",
    all: "all",
  };
  const warehouses = {
    fbaUs: "US",
    fbaEu: "EU",
    fbaCa: "CA",
    fbaUk: "UK",
    fbaAu: "AU",
    iwaNj: "NJ",
  }

  const stats = []; // Collect statistics for the dashboard

  console.log(`Starting updater for channels at ${new Date().toISOString()}`);

  for (const [key, value] of Object.entries(channels)) {
    const sheetName = key;
    const url = salesUrl + value;

    try {
      console.log(`Retrieving data for channel "${value}" from URL: ${url}`);

      const response = UrlFetchApp.fetch(url);
      const contentType = response.getHeaders()["Content-Type"];
      if (!contentType || !contentType.includes("application/json")) {
        console.log(`Skipping ${sheetName}: Invalid JSON response.`);
        stats.push({ channel: key, updatedCount: 0, notUpdatedCount: 0, newRowsCount: 0, lastUpdate: "Invalid JSON" });
        continue;
      }

      const jsonData = JSON.parse(response.getContentText());
      console.log(`Successfully retrieved data for "${value}". Number of records: ${jsonData.length}`);

      const updateStats = updateSheet(sheetName, jsonData); // Get statistics from updateSheet
      stats.push({ channel: key, ...updateStats });

    } catch (error) {
      console.log(`Error updating sheet "${sheetName}": ${error.message}`);
      stats.push({ channel: key, updatedCount: 0, notUpdatedCount: 0, newRowsCount: 0, lastUpdate: "Error" });
    }
  }

  for (const [key, value] of Object.entries(warehouses)) {
    const sheetName = key;
    const url = fbaUrl + value;

    try {
      console.log(`Retrieving data for channel "${value}" from URL: ${url}`);

      const response = UrlFetchApp.fetch(url);
      const contentType = response.getHeaders()["Content-Type"];
      if (!contentType || !contentType.includes("application/json")) {
        console.log(`Skipping ${sheetName}: Invalid JSON response.`);
        stats.push({ channel: key, updatedCount: 0, notUpdatedCount: 0, newRowsCount: 0, lastUpdate: "Invalid JSON" });
        continue;
      }

      const jsonData = JSON.parse(response.getContentText());
      console.log(`Successfully retrieved data for "${value}". Number of records: ${jsonData.length}`);

      const updateStats = updateSheet(sheetName, jsonData); // Get statistics from updateSheet
      stats.push({ channel: key, ...updateStats });

    } catch (error) {
      console.log(`Error updating sheet "${sheetName}": ${error.message}`);
      stats.push({ channel: key, updatedCount: 0, notUpdatedCount: 0, newRowsCount: 0, lastUpdate: "Error" });
    }
  }

  console.log(`Completed updater for all channels at ${new Date().toISOString()}`);

  // Create or update the dashboard
  createDashboard(stats);
}

function updateSheet(sheetName, jsonData) {
  const spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = spreadsheet.getSheetByName(sheetName);

  // Create the sheet if it doesn't exist
  if (!sheet) {
    console.log(`Sheet "${sheetName}" not found. Creating a new sheet.`);
    sheet = spreadsheet.insertSheet(sheetName);
    const headers = Object.keys(jsonData[0]).concat("lastUpdate");
    sheet.appendRow(headers);
  }

  const dataRange = sheet.getDataRange();
  const existingData = dataRange.getValues();
  const headers = existingData[0];
  const iwaskuIndex = headers.indexOf("iwasku");
  const asinIndex = headers.indexOf("asin");

  if (iwaskuIndex === -1 || asinIndex === -1) {
    throw new Error(`Sheet "${sheetName}" is missing the "iwasku" or "asin" column.`);
  }

  let lastUpdateIndex = headers.indexOf("lastUpdate");
  if (lastUpdateIndex === -1) {
    lastUpdateIndex = headers.length;
    sheet.getRange(1, lastUpdateIndex + 1).setValue("lastUpdate");
  }

  const columnIndexes = headers.reduce((map, header, index) => {
    map[header] = index;
    return map;
  }, {});

  // Create a map of composite keys (iwasku + asin) to row index
  const compositeKeyToRowMap = {};
  for (let i = 1; i < existingData.length; i++) {
    const iwasku = existingData[i][iwaskuIndex];
    const asin = existingData[i][asinIndex];
    if (iwasku && asin) {
      const compositeKey = `${iwasku}_${asin}`;
      compositeKeyToRowMap[compositeKey] = i; // Store the 1-based row index
    }
  }

  let updatedCount = 0;
  let notUpdatedCount = 0;
  const newRows = [];

  const now = new Date();
  const formattedNow = Utilities.formatDate(now, spreadsheet.getSpreadsheetTimeZone(), "yyyy-MM-dd HH:mm:ss");

  jsonData.forEach(item => {
    const iwasku = item.iwasku;
    const asin = item.asin;
    const compositeKey = `${iwasku}_${asin}`;

    if (compositeKeyToRowMap[compositeKey] !== undefined) {
      const rowIndex = compositeKeyToRowMap[compositeKey];
      let updated = false;

      // Check for changes in the row
      Object.keys(item).forEach(field => {
        const fieldIndex = columnIndexes[field];
        if (fieldIndex !== undefined) {
          const existingValue = existingData[rowIndex][fieldIndex];
          const newValue = item[field] === null ? "" : item[field];

          if (existingValue !== newValue) {
            existingData[rowIndex][fieldIndex] = newValue; // Update in memory
            updated = true;
          }
        }
      });

      // Update lastUpdate column only if changes occurred
      if (updated) {
        existingData[rowIndex][lastUpdateIndex] = formattedNow; // Update lastUpdate in memory
        updatedCount++;
      } else {
        notUpdatedCount++;
      }
    } else {
      // Append a new row if the composite key is not found
      const newRow = headers.map(header => (header === "lastUpdate" ? formattedNow : item[header] === null ? "" : item[header] || ""));
      newRows.push(newRow);
    }
  });

  // Write updates back to the sheet
  if (existingData.length > 1) {
    const updateRange = sheet.getRange(2, 1, existingData.length - 1, existingData[0].length);
    updateRange.setValues(existingData.slice(1)); // Skip the header row
  }

  // Append new rows in one operation
  if (newRows.length > 0) {
    sheet.getRange(sheet.getLastRow() + 1, 1, newRows.length, newRows[0].length).setValues(newRows);
  }

  // Return statistics for the dashboard
  return { updatedCount, notUpdatedCount, newRowsCount: newRows.length, lastUpdate: formattedNow };
}


function createDashboard(stats) {
  const spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = spreadsheet.getSheetByName("BENIOKU");

  // Create the BENIOKU sheet if it doesn't exist
  if (!sheet) {
    sheet = spreadsheet.insertSheet("BENIOKU");
  } else {
    sheet.clear(); // Clear existing content
  }

  // Format the sheet for the dashboard
  sheet.setColumnWidths(1, 3, 200); // Set column widths for better visibility

  // Add the title
  sheet.getRange("A1").setValue("BENIOKU Dashboard").setFontSize(16).setFontWeight("bold").setHorizontalAlignment("center");
  sheet.getRange("A1:C1").merge(); // Corrected merging of cells

  // Add headers for the summary table
  const headers = ["Channel", "Rows Updated", "Rows Not Updated", "New Rows Added", "Last Update"];
  sheet.getRange("A3:E3").setValues([headers]).setFontWeight("bold").setBackground("#d9d9d9");

  // Populate the data
  const data = stats.map(stat => [
    stat.channel,
    stat.updatedCount,
    stat.notUpdatedCount,
    stat.newRowsCount,
    stat.lastUpdate
  ]);
  sheet.getRange(4, 1, data.length, data[0].length).setValues(data);

  // Add some conditional formatting to highlight important numbers
  const range = sheet.getRange(4, 2, data.length, 3); // Apply to "Rows Updated", "Rows Not Updated", and "New Rows Added"
  const rule = SpreadsheetApp.newConditionalFormatRule()
    .setRanges([range])
    .whenNumberGreaterThan(0)
    .setBackground("#c6efce") // Light green for positive numbers
    .setFontColor("#006100")
    .build();
  const rules = sheet.getConditionalFormatRules();
  rules.push(rule);
  sheet.setConditionalFormatRules(rules);

  // Freeze the header row
  sheet.setFrozenRows(3);

  // Set alignment for better readability
  sheet.getDataRange().setHorizontalAlignment("center");

  console.log("Dashboard updated in sheet BENIOKU.");
}
