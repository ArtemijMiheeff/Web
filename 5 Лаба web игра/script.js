var count = 0;
var max = 100;
var right_side = 664;
var bottom = 350;
var start_time = 16;

const button1 = document.querySelector('button');
button1.addEventListener('click', Show_Image);

function Show_Image()
{

    var goal = document.createElement("img");
    
    goal.src = 'goal.jpg';
    goal.id = 'myImage';
    
    let container1 = document.getElementById('img_on_screen');
    container1.appendChild(goal);
    
    get_random_number_and_move(max, right_side, bottom, count);

    Time_to_zero();

};

function get_random_number_and_move(max, right_side, bottom, count)
{
    Check_position();
    var x = Math.floor(Math.random() * max)/0.1;
    while(x>right_side)
        {
            var x = Math.floor(Math.random() * max)/0.1;
        }
    var y = Math.floor(Math.random() * max)/0.1;
    while(y>bottom)
        {
            var y = Math.floor(Math.random() * max)/0.1;
        }
    
    Change_sign(x,y);
    
    var img = document.getElementById('myImage'), pos_x = 0, pos_y=0;

    img.onclick = function()
    {
        img.style.left = pos_x + x + 'px';
        img.style.top = pos_y + y + 'px';
        Click_count(count);
        count++;

        get_random_number_and_move(max, right_side, bottom,count);
    }
    
}

function Check_position()
{
    var img = document.getElementById('myImage'), pos_x = 0, pos_y=0;
    
    var pos = img.getBoundingClientRect();
    console.log(pos.top, pos.right, pos.bottom, pos.left);
}

function Change_sign(x, y)
{
    var one = 2;
    var sign = Math.floor(Math.random() * one);
    console.log(sign);
    if (sign==0)
        {
            x = -x;
        }
    if (sign==1)
        {
            y = -y;
        }
}

function Click_count(count)
{
    var cnt = document.getElementById("count");
    count = count + 1;
    cnt.innerHTML = count;
    setTimeout(10);
}

function Time_to_zero() {

	var time = document.getElementById("timer1");

	if (start_time > 0) {
		console.log('Time: ' + start_time);
		start_time--;
		setTimeout(Time_to_zero,1000)
		time.innerHTML = start_time + 's';
		if (start_time == 0) 
		{
            return;
		}
	}
}

Check_position();
Change_sign(1,1);