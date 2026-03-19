<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <!-- <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3> -->
    <?php $__env->endSlot(); ?>

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New Branch <i class="icofont icofont-plus-circle"></i></button></li>
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModalSearch">Search Branch <i class="icofont icofont-plus-circle"></i></button></li> -->
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModalMapping">Branch Maping<i class="icofont icofont-plus-circle"></i></button></li> -->
 
    <?php $__env->endSlot(); ?>
    
    <!-- <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    -->
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">

 
                  <div class="card-body">
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        <?php echo e($error); ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
                  <?php if($message = Session::get('success')): ?>
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        <?php echo $message; ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					<?php endif; ?>

                      <p>
                      <div class="table-responsive">
                       
                      
						<table class="table table-xs">
							<thead>
								<tr>
                               
                                <th scope="col">#</th>
									
                                    <th scope="col">branch code</th>
                                    <th scope="col">branch name</th>
                                    <th scope="col">district</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                    
								</tr>
							</thead>
							<tbody>
                          
                            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
                               
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>
								    
                                    <td><?php echo e($loan['BRANCH_CODE']); ?></td>
                                    <td><?php echo e(strtoupper($loan['BRANCH_NAME'])); ?></td>
                                    <td><?php echo e(App\Http\Controllers\API\Auth\CustomersController::getDistrictName($loan['DISTRICT_CODE'])); ?> (<?php echo e($loan['DISTRICT_CODE']); ?>)</td>
                                    <td><?php echo e($loan['STATUS']); ?></td>
                                    <td> 
                                        <?php if($loan['STATUS'] === 'Active'): ?>
                                        <div class="pull-right"> <a href='<?php echo Route('branch-mapping-update', ['id' => $loan['ID'], 'status' => 'Inactive']); ?>' class='btn btn-outline-danger btn-xs'>Deactivate </a></div>
                                        <?php elseif($loan['STATUS'] === 'Inactive'): ?>
                                        <div class="pull-right"> <a href='<?php echo Route('branch-mapping-update', ['id' => $loan['ID'], 'status' => 'Active']); ?>' class='btn btn-outline-primary btn-xs'>Activate </a></div>
                                        <?php else: ?>
                                        <?php endif; ?>

                                    </td>
                                    
								</tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
                        <br />
                        <?php echo e($loans->links()); ?>

                        <button wire:click="publishBranch" class="btn btn-outline-primary btn-xs pull-right"  type="button" wire:loading.remove> Publish Branches <i class="icofont icofont-ui-rate-add"></i></button>
                         
                        <div wire:loading.delay>
                        <div class="loader-box">
                            <div class="loader-7" style="width: 50px; height:50px;"></div>
                            <br/>
                                <h5 class="f-w-100">Processing ...</h5>
                            </div>
                        </div>
                    </div>
                        
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Branch  Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="v1/intermediary/branches/add">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- <div class="form-group">
                                <label class="col-form-label" >Branch Code</label>
                                <input class="form-control" type="number"  required  name="BRANCH_CODE">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Branch Name</label>
                                <input class="form-control" type="text" required  name="BRANCH_NAME">
                            </div> -->
                            <div class="form-group">
                            <label class="col-form-label" >Select Mwalimu Bank Branch</label>
                                <select class="form-select" required name="BRANCH_CODE">
                                <option value="">--- Choose Branch ---</option>  
                                        <?php $__currentLoopData = $distinctBranchCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e(strtoupper($branch->BRANCH_CODE)); ?>'><?php echo e(strtoupper($branch->BRANCH_NAME)); ?> (<?php echo e(strtoupper($branch->BRANCH_CODE)); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Select District</label>
                                <select class="form-select" required name="DISTRICT_CODE">
                                <option value="DIS1">ILALA</option>
                                <option value="DIS2">ILALA CBD</option>
                                <option value="DIS3">KIGAMBONI</option>
                                <option value="DIS4">KINONDONI</option>
                                <option value="DIS5">TEMEKE</option>
                                <option value="DIS6">UBUNGO</option>
                                <option value="DIS7">HANDENI</option>
                                <option value="DIS8">KILINDI</option>
                                <option value="DIS9">KOROGWE</option>
                                <option value="DIS10">LUSHOTO</option>
                                <option value="DIS11">MKINGA</option>
                                <option value="DIS12">MUHEZA</option>
                                <option value="DIS13">PANGANI</option>
                                <option value="DIS14">TANGA</option>
                                <option value="DIS15">TANGA CBD</option>
                                <option value="DIS16">ARUSHA</option>
                                <option value="DIS17">ARUSHA CBD</option>
                                <option value="DIS18">KARATU</option>
                                <option value="DIS19">LONGIDO</option>
                                <option value="DIS20">MERU</option>
                                <option value="DIS21">MONDULI</option>
                                <option value="DIS22">NGORONGORO</option>
                                <option value="DIS23">HAI</option>
                                <option value="DIS24">MOSHI</option>
                                <option value="DIS25">MOSHI CBD</option>
                                <option value="DIS26">MWANGA</option>
                                <option value="DIS27">ROMBO</option>
                                <option value="DIS28">SAME</option>
                                <option value="DIS29">SIHA</option>
                                <option value="DIS30">BABATI</option>
                                <option value="DIS31">BABATI CBD</option>
                                <option value="DIS32">HANANG'</option>
                                <option value="DIS33">KITETO</option>
                                <option value="DIS34">MBULU</option>
                                <option value="DIS35">SIMANJIRO</option>
                                <option value="DIS36">BUKOMBE</option>
                                <option value="DIS37">CHATO</option>
                                <option value="DIS38">GEITA</option>
                                <option value="DIS39">MBOGWE</option>
                                <option value="DIS40">NYANG'HWALE</option>
                                <option value="DIS41">BUNDA</option>
                                <option value="DIS42">BUTIAMA</option>
                                <option value="DIS43">MUSOMA CBD</option>
                                <option value="DIS44">RORYA</option>
                                <option value="DIS45">SERENGETI</option>
                                <option value="DIS46">TARIME</option>
                                <option value="DIS47">ILEMELA</option>
                                <option value="DIS48">KWIMBA</option>
                                <option value="DIS49">MAGU</option>
                                <option value="DIS50">MISUNGWI</option>
                                <option value="DIS51">NYAMAGANA</option>
                                <option value="DIS52">SENGEREMA</option>
                                <option value="DIS53">UKEREWE</option>
                                <option value="DIS54">BIHARAMULO</option>
                                <option value="DIS55">BUKOBA</option>
                                <option value="DIS56">BUKOBA CBD</option>
                                <option value="DIS57">KARAGWE</option>
                                <option value="DIS58">KYERWA</option>
                                <option value="DIS59">MISENYI</option>
                                <option value="DIS60">MULEBA</option>
                                <option value="DIS61">NGARA</option>
                                <option value="DIS62">KAHAMA</option>
                                <option value="DIS63">KISHAPU</option>
                                <option value="DIS64">SHINYANGA</option>
                                <option value="DIS65">SHINYANGA CBD</option>
                                <option value="DIS66">BARIADI</option>
                                <option value="DIS67">BUSEGA</option>
                                <option value="DIS68">ITILIMA</option>
                                <option value="DIS69">MASWA</option>
                                <option value="DIS70">MEATU</option>
                                <option value="DIS71">BAHI</option>
                                <option value="DIS72">CHAMWINO</option>
                                <option value="DIS73">CHEMBA</option>
                                <option value="DIS74">DODOMA</option>
                                <option value="DIS75">DODOMA CBD</option>
                                <option value="DIS76">KONDOA</option>
                                <option value="DIS77">KONGWA</option>
                                <option value="DIS78">MPWAPWA</option>
                                <option value="DIS79">IKUNGI</option>
                                <option value="DIS80">IRAMBA</option>
                                <option value="DIS81">MANYONI</option>
                                <option value="DIS82">MKALAMA</option>
                                <option value="DIS83">SINGIDA</option>
                                <option value="DIS84">SINGIDA CBD</option>
                                <option value="DIS85">IGUNGA</option>
                                <option value="DIS86">KALIUA</option>
                                <option value="DIS87">NZEGA</option>
                                <option value="DIS88">SIKONGE</option>
                                <option value="DIS89">TABORA CBD</option>
                                <option value="DIS90">URAMBO</option>
                                <option value="DIS91">UYUI</option>
                                <option value="DIS92">BUHIGWE</option>
                                <option value="DIS93">KAKONKO</option>
                                <option value="DIS94">KASULU</option>
                                <option value="DIS95">KIBONDO</option>
                                <option value="DIS96">KIGOMA</option>
                                <option value="DIS97">KIGOMA CBD</option>
                                <option value="DIS98">UVINZA</option>
                                <option value="DIS99">MLELE</option>
                                <option value="DIS100">MPANDA CBD</option>
                                <option value="DIS101">TANGANYIKA</option>
                                <option value="DIS102">IRINGA</option>
                                <option value="DIS103">IRINGA CBD</option>
                                <option value="DIS104">KILOLO</option>
                                <option value="DIS105">MUFINDI</option>
                                <option value="DIS106">CHUNYA</option>
                                <option value="DIS107">KYELA</option>
                                <option value="DIS108">MBARALI</option>
                                <option value="DIS109">MBEYA</option>
                                <option value="DIS110">MBEYA CBD</option>
                                <option value="DIS111">RUNGWE</option>
                                <option value="DIS112">ILEJE</option>
                                <option value="DIS113">MBOZI</option>
                                <option value="DIS114">MOMBA</option>
                                <option value="DIS115">SONGWE</option>
                                <option value="DIS116">KALAMBO</option>
                                <option value="DIS117">NKASI</option>
                                <option value="DIS118">SUMBAWANGA</option>
                                <option value="DIS119">SUMBAWANGA CBD</option>
                                <option value="DIS120">MBINGA</option>
                                <option value="DIS121">NYASA</option>
                                <option value="DIS122">SONGEA</option>
                                <option value="DIS123">SONGEA CBD</option>
                                <option value="DIS124">TUNDURU</option>
                                <option value="DIS125">LUDEWA</option>
                                <option value="DIS126">NJOMBE</option>
                                <option value="DIS127">NJOMBE CBD</option>
                                <option value="DIS128">WANGING'OMBE</option>
                                <option value="DIS129">BAGAMOYO</option>
                                <option value="DIS130">KIBAHA</option>
                                <option value="DIS131">KIBAHA CBD</option>
                                <option value="DIS132">KIBITI</option>
                                <option value="DIS133">KISARAWE</option>
                                <option value="DIS134">MAFIA</option>
                                <option value="DIS135">MKURANGA</option>
                                <option value="DIS136">RUFIJI</option>
                                <option value="DIS137">MASASI</option>
                                <option value="DIS138">MTWARA</option>
                                <option value="DIS139">MTWARA CBD</option>
                                <option value="DIS140">NANYUMBU</option>
                                <option value="DIS141">NEWALA</option>
                                <option value="DIS142">TANDAHIMBA</option>
                                <option value="DIS143">KILWA</option>
                                <option value="DIS144">LINDI</option>
                                <option value="DIS145">LINDI CBD</option>
                                <option value="DIS146">LIWALE</option>
                                <option value="DIS147">NACHINGWEA</option>
                                <option value="DIS148">RUANGWA</option>
                                <option value="DIS149">GAIRO</option>
                                <option value="DIS150">KILOMBERO</option>
                                <option value="DIS151">KILOSA</option>
                                <option value="DIS152">MALINYI</option>
                                <option value="DIS153">MOROGORO</option>
                                <option value="DIS154">MOROGORO CBD</option>
                                <option value="DIS155">MVOMERO</option>
                                <option value="DIS156">ULANGA</option>
                                <option value="DIS157">MAGHARIBI "A"</option>
                                <option value="DIS158">MAGHARIBI "B"</option>
                                <option value="DIS159">MJINI</option>
                                <option value="DIS160">KATI</option>
                                <option value="DIS161">KUSINI</option>
                                <option value="DIS162">KASKAZINI A</option>
                                <option value="DIS163">KASKAZINI B</option>
                                <option value="DIS164">CHAKECHAKE</option>
                                <option value="DIS165">MKOANI</option>
                                <option value="DIS166">MICHEWENI</option>
                                <option value="DIS167">WETE</option>
                                <option value="DIS168">MAKETE</option>
                                <option value="DIS170">NAMTUMBO</option>
                                <option value="DIS171">CHALINZE</option>
                                <option value="DIS201">MUSOMA</option>
                                <option value="DIS202">Geita CDB</option>

                                   
								</select>
                            </div>
                            
                            
                        </div>

                        <div class="col-lg-6">
                           
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>

     <!-- NEW MODAL START -->
 <div class="modal fade" id="newModalSearch" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Branch  Search</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
        
            <form method="post" action="<?php echo e(Route('branches-list')); ?>">
                
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="form-group">
                                <label class="col-form-label" >Select District</label>
                                <select class="form-select" required name="DISTRICT_CODE">
                                <option value="DIS1">ILALA</option>
                                <option value="DIS2">ILALA CBD</option>
                                <option value="DIS3">KIGAMBONI</option>
                                <option value="DIS4">KINONDONI</option>
                                <option value="DIS5">TEMEKE</option>
                                <option value="DIS6">UBUNGO</option>
                                <option value="DIS7">HANDENI</option>
                                <option value="DIS8">KILINDI</option>
                                <option value="DIS9">KOROGWE</option>
                                <option value="DIS10">LUSHOTO</option>
                                <option value="DIS11">MKINGA</option>
                                <option value="DIS12">MUHEZA</option>
                                <option value="DIS13">PANGANI</option>
                                <option value="DIS14">TANGA</option>
                                <option value="DIS15">TANGA CBD</option>
                                <option value="DIS16">ARUSHA</option>
                                <option value="DIS17">ARUSHA CBD</option>
                                <option value="DIS18">KARATU</option>
                                <option value="DIS19">LONGIDO</option>
                                <option value="DIS20">MERU</option>
                                <option value="DIS21">MONDULI</option>
                                <option value="DIS22">NGORONGORO</option>
                                <option value="DIS23">HAI</option>
                                <option value="DIS24">MOSHI</option>
                                <option value="DIS25">MOSHI CBD</option>
                                <option value="DIS26">MWANGA</option>
                                <option value="DIS27">ROMBO</option>
                                <option value="DIS28">SAME</option>
                                <option value="DIS29">SIHA</option>
                                <option value="DIS30">BABATI</option>
                                <option value="DIS31">BABATI CBD</option>
                                <option value="DIS32">HANANG'</option>
                                <option value="DIS33">KITETO</option>
                                <option value="DIS34">MBULU</option>
                                <option value="DIS35">SIMANJIRO</option>
                                <option value="DIS36">BUKOMBE</option>
                                <option value="DIS37">CHATO</option>
                                <option value="DIS38">GEITA</option>
                                <option value="DIS39">MBOGWE</option>
                                <option value="DIS40">NYANG'HWALE</option>
                                <option value="DIS41">BUNDA</option>
                                <option value="DIS42">BUTIAMA</option>
                                <option value="DIS43">MUSOMA CBD</option>
                                <option value="DIS44">RORYA</option>
                                <option value="DIS45">SERENGETI</option>
                                <option value="DIS46">TARIME</option>
                                <option value="DIS47">ILEMELA</option>
                                <option value="DIS48">KWIMBA</option>
                                <option value="DIS49">MAGU</option>
                                <option value="DIS50">MISUNGWI</option>
                                <option value="DIS51">NYAMAGANA</option>
                                <option value="DIS52">SENGEREMA</option>
                                <option value="DIS53">UKEREWE</option>
                                <option value="DIS54">BIHARAMULO</option>
                                <option value="DIS55">BUKOBA</option>
                                <option value="DIS56">BUKOBA CBD</option>
                                <option value="DIS57">KARAGWE</option>
                                <option value="DIS58">KYERWA</option>
                                <option value="DIS59">MISENYI</option>
                                <option value="DIS60">MULEBA</option>
                                <option value="DIS61">NGARA</option>
                                <option value="DIS62">KAHAMA</option>
                                <option value="DIS63">KISHAPU</option>
                                <option value="DIS64">SHINYANGA</option>
                                <option value="DIS65">SHINYANGA CBD</option>
                                <option value="DIS66">BARIADI</option>
                                <option value="DIS67">BUSEGA</option>
                                <option value="DIS68">ITILIMA</option>
                                <option value="DIS69">MASWA</option>
                                <option value="DIS70">MEATU</option>
                                <option value="DIS71">BAHI</option>
                                <option value="DIS72">CHAMWINO</option>
                                <option value="DIS73">CHEMBA</option>
                                <option value="DIS74">DODOMA</option>
                                <option value="DIS75">DODOMA CBD</option>
                                <option value="DIS76">KONDOA</option>
                                <option value="DIS77">KONGWA</option>
                                <option value="DIS78">MPWAPWA</option>
                                <option value="DIS79">IKUNGI</option>
                                <option value="DIS80">IRAMBA</option>
                                <option value="DIS81">MANYONI</option>
                                <option value="DIS82">MKALAMA</option>
                                <option value="DIS83">SINGIDA</option>
                                <option value="DIS84">SINGIDA CBD</option>
                                <option value="DIS85">IGUNGA</option>
                                <option value="DIS86">KALIUA</option>
                                <option value="DIS87">NZEGA</option>
                                <option value="DIS88">SIKONGE</option>
                                <option value="DIS89">TABORA CBD</option>
                                <option value="DIS90">URAMBO</option>
                                <option value="DIS91">UYUI</option>
                                <option value="DIS92">BUHIGWE</option>
                                <option value="DIS93">KAKONKO</option>
                                <option value="DIS94">KASULU</option>
                                <option value="DIS95">KIBONDO</option>
                                <option value="DIS96">KIGOMA</option>
                                <option value="DIS97">KIGOMA CBD</option>
                                <option value="DIS98">UVINZA</option>
                                <option value="DIS99">MLELE</option>
                                <option value="DIS100">MPANDA CBD</option>
                                <option value="DIS101">TANGANYIKA</option>
                                <option value="DIS102">IRINGA</option>
                                <option value="DIS103">IRINGA CBD</option>
                                <option value="DIS104">KILOLO</option>
                                <option value="DIS105">MUFINDI</option>
                                <option value="DIS106">CHUNYA</option>
                                <option value="DIS107">KYELA</option>
                                <option value="DIS108">MBARALI</option>
                                <option value="DIS109">MBEYA</option>
                                <option value="DIS110">MBEYA CBD</option>
                                <option value="DIS111">RUNGWE</option>
                                <option value="DIS112">ILEJE</option>
                                <option value="DIS113">MBOZI</option>
                                <option value="DIS114">MOMBA</option>
                                <option value="DIS115">SONGWE</option>
                                <option value="DIS116">KALAMBO</option>
                                <option value="DIS117">NKASI</option>
                                <option value="DIS118">SUMBAWANGA</option>
                                <option value="DIS119">SUMBAWANGA CBD</option>
                                <option value="DIS120">MBINGA</option>
                                <option value="DIS121">NYASA</option>
                                <option value="DIS122">SONGEA</option>
                                <option value="DIS123">SONGEA CBD</option>
                                <option value="DIS124">TUNDURU</option>
                                <option value="DIS125">LUDEWA</option>
                                <option value="DIS126">NJOMBE</option>
                                <option value="DIS127">NJOMBE CBD</option>
                                <option value="DIS128">WANGING'OMBE</option>
                                <option value="DIS129">BAGAMOYO</option>
                                <option value="DIS130">KIBAHA</option>
                                <option value="DIS131">KIBAHA CBD</option>
                                <option value="DIS132">KIBITI</option>
                                <option value="DIS133">KISARAWE</option>
                                <option value="DIS134">MAFIA</option>
                                <option value="DIS135">MKURANGA</option>
                                <option value="DIS136">RUFIJI</option>
                                <option value="DIS137">MASASI</option>
                                <option value="DIS138">MTWARA</option>
                                <option value="DIS139">MTWARA CBD</option>
                                <option value="DIS140">NANYUMBU</option>
                                <option value="DIS141">NEWALA</option>
                                <option value="DIS142">TANDAHIMBA</option>
                                <option value="DIS143">KILWA</option>
                                <option value="DIS144">LINDI</option>
                                <option value="DIS145">LINDI CBD</option>
                                <option value="DIS146">LIWALE</option>
                                <option value="DIS147">NACHINGWEA</option>
                                <option value="DIS148">RUANGWA</option>
                                <option value="DIS149">GAIRO</option>
                                <option value="DIS150">KILOMBERO</option>
                                <option value="DIS151">KILOSA</option>
                                <option value="DIS152">MALINYI</option>
                                <option value="DIS153">MOROGORO</option>
                                <option value="DIS154">MOROGORO CBD</option>
                                <option value="DIS155">MVOMERO</option>
                                <option value="DIS156">ULANGA</option>
                                <option value="DIS157">MAGHARIBI "A"</option>
                                <option value="DIS158">MAGHARIBI "B"</option>
                                <option value="DIS159">MJINI</option>
                                <option value="DIS160">KATI</option>
                                <option value="DIS161">KUSINI</option>
                                <option value="DIS162">KASKAZINI A</option>
                                <option value="DIS163">KASKAZINI B</option>
                                <option value="DIS164">CHAKECHAKE</option>
                                <option value="DIS165">MKOANI</option>
                                <option value="DIS166">MICHEWENI</option>
                                <option value="DIS167">WETE</option>
                                <option value="DIS168">MAKETE</option>
                                <option value="DIS170">NAMTUMBO</option>
                                <option value="DIS171">CHALINZE</option>
                                <option value="DIS201">MUSOMA</option>
                                <option value="DIS202">Geita CDB</option>

                                   
								</select>
                            </div>
                           
                            
                            
                        </div>

                        <div class="col-lg-6">
                           
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button wire:click="searchBybranch" class="btn btn-primary" type="submit" >Confirm & Search</button>
</form>
              </div>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->
  <!-- NEW MODAL START -->
  <div class="modal fade" id="newModalMapping" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Branch Mapping</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="v1/intermediary/branches/add">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                        
                            <label class="col-form-label" >Select Azania Branch</label>
                                <select class="form-select" required name="BRANCH_CODE">
                                <option value="">--- Choose Branch ---</option>  
                                        <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e(strtoupper($branch->BRANCH_CODE)); ?>'><?php echo e(strtoupper($branch->BRANCH_NAME)); ?> (<?php echo e(strtoupper($branch->BRANCH_CODE)); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
                          
                        
                            <div class="form-group">
                                <label class="col-form-label" >Select District</label>
                                <select class="form-select" required name="DISTRICT_CODE">
                                <option value="DIS1">ILALA</option>
                                <option value="DIS2">ILALA CBD</option>
                                <option value="DIS3">KIGAMBONI</option>
                                <option value="DIS4">KINONDONI</option>
                                <option value="DIS5">TEMEKE</option>
                                <option value="DIS6">UBUNGO</option>
                                <option value="DIS7">HANDENI</option>
                                <option value="DIS8">KILINDI</option>
                                <option value="DIS9">KOROGWE</option>
                                <option value="DIS10">LUSHOTO</option>
                                <option value="DIS11">MKINGA</option>
                                <option value="DIS12">MUHEZA</option>
                                <option value="DIS13">PANGANI</option>
                                <option value="DIS14">TANGA</option>
                                <option value="DIS15">TANGA CBD</option>
                                <option value="DIS16">ARUSHA</option>
                                <option value="DIS17">ARUSHA CBD</option>
                                <option value="DIS18">KARATU</option>
                                <option value="DIS19">LONGIDO</option>
                                <option value="DIS20">MERU</option>
                                <option value="DIS21">MONDULI</option>
                                <option value="DIS22">NGORONGORO</option>
                                <option value="DIS23">HAI</option>
                                <option value="DIS24">MOSHI</option>
                                <option value="DIS25">MOSHI CBD</option>
                                <option value="DIS26">MWANGA</option>
                                <option value="DIS27">ROMBO</option>
                                <option value="DIS28">SAME</option>
                                <option value="DIS29">SIHA</option>
                                <option value="DIS30">BABATI</option>
                                <option value="DIS31">BABATI CBD</option>
                                <option value="DIS32">HANANG'</option>
                                <option value="DIS33">KITETO</option>
                                <option value="DIS34">MBULU</option>
                                <option value="DIS35">SIMANJIRO</option>
                                <option value="DIS36">BUKOMBE</option>
                                <option value="DIS37">CHATO</option>
                                <option value="DIS38">GEITA</option>
                                <option value="DIS39">MBOGWE</option>
                                <option value="DIS40">NYANG'HWALE</option>
                                <option value="DIS41">BUNDA</option>
                                <option value="DIS42">BUTIAMA</option>
                                <option value="DIS43">MUSOMA CBD</option>
                                <option value="DIS44">RORYA</option>
                                <option value="DIS45">SERENGETI</option>
                                <option value="DIS46">TARIME</option>
                                <option value="DIS47">ILEMELA</option>
                                <option value="DIS48">KWIMBA</option>
                                <option value="DIS49">MAGU</option>
                                <option value="DIS50">MISUNGWI</option>
                                <option value="DIS51">NYAMAGANA</option>
                                <option value="DIS52">SENGEREMA</option>
                                <option value="DIS53">UKEREWE</option>
                                <option value="DIS54">BIHARAMULO</option>
                                <option value="DIS55">BUKOBA</option>
                                <option value="DIS56">BUKOBA CBD</option>
                                <option value="DIS57">KARAGWE</option>
                                <option value="DIS58">KYERWA</option>
                                <option value="DIS59">MISENYI</option>
                                <option value="DIS60">MULEBA</option>
                                <option value="DIS61">NGARA</option>
                                <option value="DIS62">KAHAMA</option>
                                <option value="DIS63">KISHAPU</option>
                                <option value="DIS64">SHINYANGA</option>
                                <option value="DIS65">SHINYANGA CBD</option>
                                <option value="DIS66">BARIADI</option>
                                <option value="DIS67">BUSEGA</option>
                                <option value="DIS68">ITILIMA</option>
                                <option value="DIS69">MASWA</option>
                                <option value="DIS70">MEATU</option>
                                <option value="DIS71">BAHI</option>
                                <option value="DIS72">CHAMWINO</option>
                                <option value="DIS73">CHEMBA</option>
                                <option value="DIS74">DODOMA</option>
                                <option value="DIS75">DODOMA CBD</option>
                                <option value="DIS76">KONDOA</option>
                                <option value="DIS77">KONGWA</option>
                                <option value="DIS78">MPWAPWA</option>
                                <option value="DIS79">IKUNGI</option>
                                <option value="DIS80">IRAMBA</option>
                                <option value="DIS81">MANYONI</option>
                                <option value="DIS82">MKALAMA</option>
                                <option value="DIS83">SINGIDA</option>
                                <option value="DIS84">SINGIDA CBD</option>
                                <option value="DIS85">IGUNGA</option>
                                <option value="DIS86">KALIUA</option>
                                <option value="DIS87">NZEGA</option>
                                <option value="DIS88">SIKONGE</option>
                                <option value="DIS89">TABORA CBD</option>
                                <option value="DIS90">URAMBO</option>
                                <option value="DIS91">UYUI</option>
                                <option value="DIS92">BUHIGWE</option>
                                <option value="DIS93">KAKONKO</option>
                                <option value="DIS94">KASULU</option>
                                <option value="DIS95">KIBONDO</option>
                                <option value="DIS96">KIGOMA</option>
                                <option value="DIS97">KIGOMA CBD</option>
                                <option value="DIS98">UVINZA</option>
                                <option value="DIS99">MLELE</option>
                                <option value="DIS100">MPANDA CBD</option>
                                <option value="DIS101">TANGANYIKA</option>
                                <option value="DIS102">IRINGA</option>
                                <option value="DIS103">IRINGA CBD</option>
                                <option value="DIS104">KILOLO</option>
                                <option value="DIS105">MUFINDI</option>
                                <option value="DIS106">CHUNYA</option>
                                <option value="DIS107">KYELA</option>
                                <option value="DIS108">MBARALI</option>
                                <option value="DIS109">MBEYA</option>
                                <option value="DIS110">MBEYA CBD</option>
                                <option value="DIS111">RUNGWE</option>
                                <option value="DIS112">ILEJE</option>
                                <option value="DIS113">MBOZI</option>
                                <option value="DIS114">MOMBA</option>
                                <option value="DIS115">SONGWE</option>
                                <option value="DIS116">KALAMBO</option>
                                <option value="DIS117">NKASI</option>
                                <option value="DIS118">SUMBAWANGA</option>
                                <option value="DIS119">SUMBAWANGA CBD</option>
                                <option value="DIS120">MBINGA</option>
                                <option value="DIS121">NYASA</option>
                                <option value="DIS122">SONGEA</option>
                                <option value="DIS123">SONGEA CBD</option>
                                <option value="DIS124">TUNDURU</option>
                                <option value="DIS125">LUDEWA</option>
                                <option value="DIS126">NJOMBE</option>
                                <option value="DIS127">NJOMBE CBD</option>
                                <option value="DIS128">WANGING'OMBE</option>
                                <option value="DIS129">BAGAMOYO</option>
                                <option value="DIS130">KIBAHA</option>
                                <option value="DIS131">KIBAHA CBD</option>
                                <option value="DIS132">KIBITI</option>
                                <option value="DIS133">KISARAWE</option>
                                <option value="DIS134">MAFIA</option>
                                <option value="DIS135">MKURANGA</option>
                                <option value="DIS136">RUFIJI</option>
                                <option value="DIS137">MASASI</option>
                                <option value="DIS138">MTWARA</option>
                                <option value="DIS139">MTWARA CBD</option>
                                <option value="DIS140">NANYUMBU</option>
                                <option value="DIS141">NEWALA</option>
                                <option value="DIS142">TANDAHIMBA</option>
                                <option value="DIS143">KILWA</option>
                                <option value="DIS144">LINDI</option>
                                <option value="DIS145">LINDI CBD</option>
                                <option value="DIS146">LIWALE</option>
                                <option value="DIS147">NACHINGWEA</option>
                                <option value="DIS148">RUANGWA</option>
                                <option value="DIS149">GAIRO</option>
                                <option value="DIS150">KILOMBERO</option>
                                <option value="DIS151">KILOSA</option>
                                <option value="DIS152">MALINYI</option>
                                <option value="DIS153">MOROGORO</option>
                                <option value="DIS154">MOROGORO CBD</option>
                                <option value="DIS155">MVOMERO</option>
                                <option value="DIS156">ULANGA</option>
                                <option value="DIS157">MAGHARIBI "A"</option>
                                <option value="DIS158">MAGHARIBI "B"</option>
                                <option value="DIS159">MJINI</option>
                                <option value="DIS160">KATI</option>
                                <option value="DIS161">KUSINI</option>
                                <option value="DIS162">KASKAZINI A</option>
                                <option value="DIS163">KASKAZINI B</option>
                                <option value="DIS164">CHAKECHAKE</option>
                                <option value="DIS165">MKOANI</option>
                                <option value="DIS166">MICHEWENI</option>
                                <option value="DIS167">WETE</option>
                                <option value="DIS168">MAKETE</option>
                                <option value="DIS170">NAMTUMBO</option>
                                <option value="DIS171">CHALINZE</option>
                                <option value="DIS201">MUSOMA</option>
                                <option value="DIS202">Geita CDB</option>

                                   
								</select>
                            </div>
                            
                            
                        </div>

                        <div class="col-lg-6">
                           
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->

  <?php $__env->startPush('scripts'); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/livewire/branch-submit.blade.php ENDPATH**/ ?>