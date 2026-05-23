// pergi ke add page
function goToAddPage() 
{
  window.location.href = "addboard.html";
}

// back page
function goBack() 
{
  window.location.href = "petcommunity.html";
}

// delete board
function deletePost(btn) 
{
  btn.closest(".post").remove();
}

// button comment
function comment() 
{
  alert("Comment feature coming soon!");
}

// add board
function addPost(event) 
{
  event.preventDefault();

  let title = document.getElementById("title").value;
  let desc = document.getElementById("desc").value;

  let post = { title, desc };

  let posts = JSON.parse(localStorage.getItem("posts")) || [];
  posts.push(post);
  localStorage.setItem("posts", JSON.stringify(posts));

  alert("Post added!");
  window.location.href = "petcommunity.html";
}
window.onload = function () 
{
  let posts = JSON.parse(localStorage.getItem("posts")) || [];
  let container = document.getElementById("postList");

  if (!container) return;

  posts.forEach(p => 
    {
    let div = document.createElement("div");
    div.className = "post";

    div.innerHTML = `
      <img src="">
      <div class="post-content">
        <h3>${p.title}</h3>
        <p>${p.desc}</p>
        <div class="post-actions">
          <button onclick="comment()">Comment</button>
          <button onclick="deletePost(this)">Delete</button>
        </div>
      </div>
    `;

    container.appendChild(div);
  });
};
