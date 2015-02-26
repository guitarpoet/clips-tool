function get_row_width(boxes) {
	if(!boxes.length)
		return 0;
	return boxes.eq(0).width();
}

function get_row_height(boxes) {
	var total = 0;
	var heights = {};
	for(var i = 0; i < boxes.length; i++) {
		total += boxes.eq(i).height();
		var key = boxes.eq(i).height().toString();
		if(!heights[key]) {
			heights[key] = 1;
		}
		else
			heights[key]++;
	}
	var avg_height = total / boxes.length;
	total = 0;
	var row_height = 0;
	var avg = 0;
	for(var key in heights) {
		var count = heights[key];
		var h = parseInt(key);
		var c_avg = Math.abs(h - avg_height) / count;
		if(avg == 0 || c_avg < avg) {
			row_height = h;
			avg = c_avg;
		}
	}
	return row_height;
}

function next_row() {
	row++;
	col = 0;
}

function get_position(layout_data, columns) {
	for(var i = 0; i < layout_data.length; i++) {
		var data = layout_data[i];
		for(var j = 0; j < columns; j++) {
			if(data[j] == -1) {
				return [i, j];
			}
		}
	}
	new_layout_row(layout_data, columns);
	return [layout_data.length - 1, 0];
}

function new_layout_row(layout_data, columns) {
	var new_row = [];
	for(var i = 0; i < columns; i++) {
		new_row.push(-1);
	}
	layout_data.push(new_row);
}

function calc_layout_data(boxes, layout_data, cols, mb) {
	var row_height = get_row_height(boxes);
	for(var i = 0; i < boxes.length; i++) {
		var box = boxes.eq(i);
		var box_row_height = Math.ceil(box.height() / (mb + row_height));
		var pos = get_position(layout_data, cols);
		for(var j = pos[0]; j < pos[0] + box_row_height; j++) {
			if(j > layout_data.length -1)
				new_layout_row(layout_data, cols);
			layout_data[j][pos[1]] = i;
		}
	}
	return layout_data;
}

function get_flow_position(flow_layout_data, columns) {
	for(var i = 0; i < flow_layout_data.length; i++) {
		var ret = null;
		var data = flow_layout_data[i];
		for(var j = 0; j < columns; j++) {
			if(ret == null && data[j] == -1) {
				ret = [i, j];
			}
			else {
				if(data[j] != -1) {
					ret = null;
				}
			}	
		}
		if(ret != null)
			return ret;
	}
	new_layout_row(flow_layout_data, columns);
	return [flow_layout_data.length - 1, 0];
}

function calc_flow_layout_data(boxes, flow_layout_data, cols, mb) {
	var row_height = get_row_height(boxes);
	for(var i = 0; i < boxes.length; i++) {
		var box = boxes.eq(i);
		var box_row_height = Math.ceil((box.height() + mb) / (mb + row_height));
		var pos = get_flow_position(flow_layout_data, cols);
		for(var j = pos[0]; j < pos[0] + box_row_height; j++) {
			if(j > flow_layout_data.length -1)
				new_layout_row(flow_layout_data, cols);
			flow_layout_data[j][pos[1]] = i;
		}
	}
	return flow_layout_data;
}

function fetch_pos(index, layout_data) {
	for(var i = 0; i < layout_data.length; i++) {
		for(var j = 0; j <layout_data[i].length; j++) {
			if(layout_data[i][j] == index)
				return [i, j];
		}
	}
	return [-1, -1];
}

function cal_offset(lp, flp, items, layout_data, flow_layout_data, row_width, row_height, mb, mr) {
	return [cal_top(lp, flp, items, layout_data, flow_layout_data, row_height, mb), (lp[1] - flp[1]) * (row_width + mr)];
}

function cal_col_pos(row, col, items, layout_data, mb) {
	var i = 0;
	var total = 0;
	var last = -1;

	while(i < row) {
		if(layout_data[i][col] == last) {
			i++;
			continue;
		}
		last = layout_data[i][col];
		total += items.eq(layout_data[i][col]).height() + mb;
		i++;
	}
	return total;
}

function get_flow_row_height(row, flow_layout_data, items, mb) {
	var row_data = flow_layout_data[row];
	var h = 0;
	var row_height = get_row_height(items);
	for(var i = row_data.length - 1; i > 0; i--) {
		var cell = row_data[i];
		if(cell == -1)
			continue;
		var item = items.eq(cell);
		var pos = fetch_pos(cell, flow_layout_data);
		h = item.height();
		if(pos[0] != row) {
			for(var j = pos[0]; j < row; j++) {
				h -= (get_flow_row_height(j, flow_layout_data, items, mb) + mb);
			}
		}
		if(h < row_height) {
			return h;
		}
		return row_height;
	}
}

function get_layout_row_height(row, layout_data, items, mb) {
	var row_data = flow_layout_data[row];
	var h = 0;
	var row_height = get_row_height(items);
	for(var i = row_data.length - 1; i > 0; i--) {
		var cell = row_data[i];
		if(cell == -1)
			continue;
		var item = items.eq(cell);
		var pos = fetch_pos(cell, flow_layout_data);
		h = item.height();
		if(pos[0] != row) {
			for(var j = pos[0]; j < row; j++) {
				h -= (get_flow_row_height(j, flow_layout_data, items, mb) + mb);
			}
		}
		if(h < row_height) {
			return h;
		}
		return row_height;
	}
}

function cal_flow_col_pos(columns, items, flow_layout_data, mb) {
	if(row == 0)
		return 0;

	var columns = flow_layout_data[row].length;
	var last_col_pos = cal_flow_col_pos(row - 1);
	return last_col_pos + items.eq(flow_layout_data[row - 1][columns - 1]).height() + mb;
}

function cal_top(lp, flp, items, layout_data, flow_layout_data, row_height, mb) {
	var layout_top = 0;
	for(var i = 0; i < flp[0]; i++) {
		layout_top += (get_flow_row_height(i, flow_layout_data, items, mb) + mb);
	}
	return (row_height + mb) * lp[0] - layout_top;
}

function layout(items, cols, mb, mr) {
	var layout_data = calc_layout_data(items, [], cols, mb);
	var flow_layout_data = calc_flow_layout_data(items, [], cols, mb);
	var row_height = get_row_height(items);
	var row_width = get_row_width(items);
	items.each(function(i, item) {
		var lp = fetch_pos(i, layout_data);
		var flp = fetch_pos(i, flow_layout_data);
		if(flp[1] == cols - 1) {
			$(item).addClass('edge');
		}
		var offset = cal_offset(lp, flp, items, layout_data, flow_layout_data, row_width, row_height, mb, mr);
		$(item).css({
			top: offset[0],
			left: offset[1] 
		});
	});
}

