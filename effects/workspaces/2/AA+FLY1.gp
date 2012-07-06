#	 workspaces/2/AA+FLY1.gp
set notitle
set ylabel
set xlabel
set xrange[  -40:   40]
set yrange[  -40:   40]
set zrange[  -39:  144]
unset border
set angles degrees
set object 1 rectangle from screen 0,0 to screen 1,1 fillcolor rgb "black" behind
unset key
set view 105,270, 2.0, 1
set style data lines
set noxtics
set noytics
set noztics
set style line 1  linetype 1 linecolor rgb "red"  linewidth 3.000 pointtype 1 pointsize default
set style line 2  linetype 2 linecolor rgb "green"  linewidth 3.000 pointtype 2 pointsize default
set style line 3  linetype 3 linecolor rgb "blue"  linewidth 3.000 pointtype 3 pointsize default
set style line 4  linetype 4 linecolor rgb "yellow"  linewidth 3.000 pointtype 4 pointsize default
set style line 5  linetype 5 linecolor rgb "cyan"  linewidth 3.000 pointtype 5 pointsize default
set style line 6  linetype 6 linecolor rgb "orange"  linewidth 3.000 pointtype 6 pointsize default
set style line 7  linetype 7 linecolor rgb "gray"  linewidth 1.000 pointtype 7 pointsize 0.3


set terminal gif animate notransparent noenhanced  delay 5 size 363,656
set output 'workspaces/2/AA+FLY1.gif'
   splot 'workspaces/2/AA+FLY1_d_1.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_2.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_3.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_4.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_5.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_6.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_7.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_8.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_9.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_10.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_11.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_12.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_13.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_14.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_15.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_16.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_17.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_18.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_19.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_20.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_21.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_22.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_23.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_24.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_25.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_26.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_27.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_28.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_29.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_30.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_31.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_32.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_33.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_34.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_35.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_36.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_37.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_38.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_39.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_40.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_41.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_42.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_43.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_44.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_45.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_46.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_47.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_48.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_49.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_50.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_51.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_52.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_53.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_54.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_55.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_56.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_57.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_58.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_59.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_60.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_61.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_62.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_63.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_64.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_65.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_66.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_67.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_68.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_69.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_70.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_71.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_72.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_73.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_74.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_75.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_76.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_77.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_78.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_79.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/AA+FLY1_d_80.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle

set output


set terminal gif animate notransparent noenhanced  delay 5 size 100,200
set output 'workspaces/2/AA+FLY1_th.gif'
   splot 'workspaces/2/AA+FLY1_d_1.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_2.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_3.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_4.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_5.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_6.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_7.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_8.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_9.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_10.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_11.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_12.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_13.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_14.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_15.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_16.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_17.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_18.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_19.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_20.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_21.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_22.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_23.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_24.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_25.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_26.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_27.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_28.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_29.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_30.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_31.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_32.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_33.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_34.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_35.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_36.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_37.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_38.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_39.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_40.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_41.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_42.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_43.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_44.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_45.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_46.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_47.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_48.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_49.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_50.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_51.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_52.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_53.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_54.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_55.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_56.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_57.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_58.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_59.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_60.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_61.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_62.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_63.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_64.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_65.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_66.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_67.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_68.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_69.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_70.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_71.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_72.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_73.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_74.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_75.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_76.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_77.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_78.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_79.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/AA+FLY1_d_80.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
