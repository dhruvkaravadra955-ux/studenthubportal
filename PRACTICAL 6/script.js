
var searchBox = document.getElementById("search");
var categoryBox = document.getElementById("category");
var sortBox = document.getElementById("sort");
var message = document.getElementById("message");
var eventList = document.getElementById("eventList");
var pagination = document.getElementById("pagination");

var allEvents = [];    
var currentPage = 1;    
var perPage = 5;        
function loadEvents() {
  message.innerText = "Loading...";   

  fetch("event.json")
    .then(function (response) {
      return response.json();         
    })
    .then(function (data) {
      allEvents = data;
      message.innerText = "";
      showEvents();
    })
    .catch(function (error) {
      message.innerText = "Error: data could not be loaded";   
      console.log(error);
    });
}


function showEvents() {
  var text = searchBox.value.toLowerCase();
  var category = categoryBox.value;
  var sortType = sortBox.value;

  // Search + Filter
  var result = allEvents.filter(function (item) {
    var matchSearch = item.title.toLowerCase().includes(text);
    var matchCategory = (category == "all" || item.category == category);
    return matchSearch && matchCategory;
  });

  // Sort
  result.sort(function (a, b) {
    if (sortType == "date") {
      return new Date(a.date) - new Date(b.date);
    } else if (sortType == "az") {
      return a.title.localeCompare(b.title);
    } else {
      return b.title.localeCompare(a.title);
    }
  });


  if (result.length == 0) {
    eventList.innerHTML = "";
    pagination.innerHTML = "";
    message.innerText = "No events found";
    return;
  }
  message.innerText = "";

  
  var start = (currentPage - 1) * perPage;
  var end = start + perPage;
  var pageData = result.slice(start, end);

  
  eventList.innerHTML = pageData.map(function (item) {
    return "<div class='card'>" +
             "<h3>" + item.title + "</h3>" +
             "<p>Category: " + item.category + "</p>" +
             "<p>Date: " + item.date + "</p>" +
             "<p>Venue: " + item.venue + "</p>" +
             "<p>" + item.description + "</p>" +
           "</div>";
  }).join("");

  var totalPages = Math.ceil(result.length / perPage);
  var buttons = "";
  for (var i = 1; i <= totalPages; i++) {
    if (i == currentPage) {
      buttons += "<button class='active' onclick='goToPage(" + i + ")'>" + i + "</button>";
    } else {
      buttons += "<button onclick='goToPage(" + i + ")'>" + i + "</button>";
    }
  }
  pagination.innerHTML = buttons;
}


function goToPage(number) {
  currentPage = number;
  showEvents();
}


function resetPage() {
  currentPage = 1;
  showEvents();
}

searchBox.addEventListener("input", resetPage);
categoryBox.addEventListener("change", resetPage);
sortBox.addEventListener("change", resetPage);


loadEvents();