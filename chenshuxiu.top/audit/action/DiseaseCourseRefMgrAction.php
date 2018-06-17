<?php
// DiseaseCourseRefMgrAction
class DiseaseCourseRefMgrAction extends AuditBaseAction
{

    public function doAddPost () {
        $courseid = XRequest::getValue("courseid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $diseasecourseref = DiseaseCourseRefDao::getByDiseaseidDoctoridCourseid($diseaseid, $doctorid ?? 0, $courseid);
        if (false == $diseasecourseref instanceof DiseaseCourseRef) {
            $row = [];
            $row['courseid'] = $courseid;
            $row['diseaseid'] = $diseaseid;
            $row['doctorid'] = $doctorid ?? 0;

            $diseasecourseref = DiseaseCourseRef::createByBiz($row);
        }

        XContext::setJumpPath("/coursemgr/modify?courseid=" . $courseid);
        return self::BLANK;
    }

    public function doDeletePost () {
        $diseasecourserefid = XRequest::getValue("diseasecourserefid", 0);
        $diseasecourseref = DiseaseCourseRef::getById($diseasecourserefid);
        $courseid = $diseasecourseref->courseid;

        $diseasecourseref->remove();
        XContext::setJumpPath("/coursemgr/modify?courseid=" . $courseid);
        return self::BLANK;
    }

    public function doAddCommon () {
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $courseid = XRequest::getValue("courseid", 0);

        if ($diseaseid && $doctorid) {
            $diseasecourserefs = DiseaseCourseRefDao::getListByDiseaseidDoctorid($diseaseid, 0);

            foreach ($diseasecourserefs as $diseasecourseref) {
                $diseasecourseref_doctor = DiseaseCourseRefDao::getByDiseaseidDoctoridCourseid($diseaseid, $doctorid, $diseasecourseref->courseid);
                if (false == $diseasecourseref_doctor instanceof DiseaseCourseRef) {
                    $row = [];
                    $row['diseaseid'] = $diseaseid;
                    $row['doctorid'] = $doctorid;
                    $row['courseid'] = $diseasecourseref->courseid;
                    $diseasecourseref_common = DiseaseCourseRef::createByBiz($row);
                }
            }
        }

        XContext::setJumpPath("/coursemgr/modify?courseid=" . $courseid);
        return self::BLANK;
    }
}
