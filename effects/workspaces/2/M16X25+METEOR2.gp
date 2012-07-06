#	 workspaces/2/M16X25+METEOR2.gp
set notitle
set ylabel
set xlabel
set xrange[  -26:   23]
set yrange[  -26:   23]
set zrange[  -22:   79]
unset border
set angles degrees
set object 1 rectangle from screen 0,0 to screen 1,1 fillcolor rgb "black" behind
unset key
set view 105, 0, 2.0, 1
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


set terminal gif animate notransparent noenhanced  delay 5 size 225,360
set output 'workspaces/2/M16X25+METEOR2.gif'
   splot 'workspaces/2/M16X25+METEOR2_d_1.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_2.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_3.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_4.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_5.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_6.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_7.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_8.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_9.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_10.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_11.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_12.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_13.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_14.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_15.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_16.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_17.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_18.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_19.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_20.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_21.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_22.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_23.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_24.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_25.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_26.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_27.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_28.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_29.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_30.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_31.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_32.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_33.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_34.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_35.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_36.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_37.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_38.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_39.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_40.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_41.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_42.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_43.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_44.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_45.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_46.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_47.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_48.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_49.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_50.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_51.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_52.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_53.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_54.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_55.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_56.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_57.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_58.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_59.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_60.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_61.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_62.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_63.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_64.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_65.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_66.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_67.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_68.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_69.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_70.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_71.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_72.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_73.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_74.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_75.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_76.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_77.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_78.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_79.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_80.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_81.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_82.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_83.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_84.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_85.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_86.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_87.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_88.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_89.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_90.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_91.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_92.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_93.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_94.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_95.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_96.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_97.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_98.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_99.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_100.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_101.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_102.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_103.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_104.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_105.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_106.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_107.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_108.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_109.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_110.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_111.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_112.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_113.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_114.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_115.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_116.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_117.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_118.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_119.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_120.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.6 notitle

set output


set terminal gif animate notransparent noenhanced  delay 5 size 100,200
set output 'workspaces/2/M16X25+METEOR2_th.gif'
   splot 'workspaces/2/M16X25+METEOR2_d_1.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_2.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_3.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_4.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_5.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_6.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_7.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_8.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_9.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_10.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_11.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_12.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_13.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_14.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_15.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_16.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_17.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_18.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_19.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_20.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_21.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_22.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_23.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_24.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_25.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_26.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_27.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_28.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_29.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_30.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_31.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_32.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_33.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_34.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_35.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_36.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_37.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_38.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_39.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_40.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_41.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_42.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_43.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_44.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_45.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_46.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_47.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_48.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_49.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_50.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_51.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_52.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_53.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_54.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_55.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_56.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_57.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_58.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_59.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_60.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_61.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_62.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_63.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_64.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_65.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_66.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_67.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_68.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_69.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_70.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_71.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_72.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_73.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_74.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_75.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_76.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_77.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_78.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_79.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_80.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_81.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_82.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_83.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_84.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_85.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_86.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_87.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_88.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_89.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_90.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_91.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_92.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_93.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_94.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_95.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_96.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_97.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_98.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_99.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_100.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_101.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_102.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_103.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_104.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_105.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_106.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_107.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_108.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_109.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_110.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_111.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_112.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_113.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_114.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_115.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_116.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_117.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_118.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_119.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
   splot 'workspaces/2/M16X25+METEOR2_d_120.dat' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize 0.3 notitle
