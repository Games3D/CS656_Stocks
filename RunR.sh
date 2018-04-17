echo Starting
module load R-Project/3.2.4
R --no-save
library(lpSolve)
library(lpSolveAPI)
model1 = read.lp("pf.txt","lp")
solve(model1)
get.objective(model1)
echo get.variables(model1)
q()
echo Ending